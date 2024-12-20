<?php declare(strict_types=1);

/*
  Copyright (c) 2023, Manticore Software LTD (https://manticoresearch.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License version 2 or any later
  version. You should have received a copy of the GPL license along with this
  program; if you did not, you can find it at http://www.gnu.org/
*/

namespace Manticoresearch\Buddy\Base\Plugin\Sharding;

use Manticoresearch\Buddy\Core\Error\QueryParseError;
use Manticoresearch\Buddy\Core\Network\Request;
use Manticoresearch\Buddy\Core\Plugin\BasePayload;

/**
 * This is simple do nothing request that handle empty queries
 * which can be as a result of only comments in it that we strip
 * @phpstan-extends BasePayload<array>
 */
final class Payload extends BasePayload {
	public string $type;
	public string $path;
	public string $cluster;
	public string $table;
	public string $structure;
	public string $extra;
	/** @var array<string,int|string> */
	public array $options;

	/**
	 * Get processors to run
	 * @return array<Processor>
	 */
	public static function getProcessors(): array {
		static $processors;
		if (!isset($processors)) {
			$processors = [
				new Processor(),
			];
		}
		return $processors;
	}

	/**
	 * Get description for this plugin
	 * @return string
	 */
	public static function getInfo(): string {
		return 'Enables sharded tables.';
	}

	/**
	 * @param Request $request
	 * @return static
	 */
	public static function fromRequest(Request $request): static {
		$pattern = '/(?:CREATE\s+TABLE|ALTER\s+TABLE)\s+'
			. '(?:(?P<cluster>[^:\s]+):)?(?P<table>[^:\s\()]+)\s*'
			. '(?:\((?P<structure>.+?)\)\s*)?'
			. '(?P<extra>.*)/ius';
		if (!preg_match($pattern, $request->payload, $matches)) {
			QueryParseError::throw('Failed to parse query');
		}

		/** @var array{table:string,cluster?:string,structure:string,extra:string} $matches */
		$options = [];
		if ($matches['extra']) {
			$pattern = '/(?P<key>rf|shards)\s*=\s*(?P<value>\'?\d+\'?)/';
			if (preg_match_all($pattern, $matches['extra'], $optionMatches, PREG_SET_ORDER)) {
				foreach ($optionMatches as $optionMatch) {
					$key = strtolower($optionMatch['key']);
					$value = (int)$optionMatch['value'];
					$options[$key] = $value;
				}
			}
			// Clean up extra from extracted options
			$matches['extra'] = trim(preg_replace($pattern, '', $matches['extra']) ?? '');
		}

		$self = new static();
		// We just need to do something, but actually its' just for PHPstan
		$self->path = $request->path;
		$self->type = strpos($request->payload, 'create') === 0 ? 'create' : 'alter';
		$self->cluster = $matches['cluster'] ?? '';
		$self->table = $matches['table'];
		$self->structure = $matches['structure'];
		$self->options = $options;
		$self->extra = $matches['extra'];
		$self->validate();
		return $self;
	}

	/**
	 * @param Request $request
	 * @return bool
	 */
	public static function hasMatch(Request $request): bool {
		return stripos($request->error, 'syntax error')
			&& strpos($request->error, 'P03') === 0
			&& (
				stripos($request->payload, 'create table') === 0
				)
			&& stripos($request->payload, 'rf') !== false
			&& stripos($request->payload, 'shards') !== false
			&& preg_match('/(?P<key>rf|shards)\s*=\s*(?P<value>[\'"]?\d+[\'"]?)/', $request->payload);
	}

	/**
	 * Run query parsed data validation
	 * @return void
	 */
	protected function validate(): void {
		if (!$this->cluster && $this->options['rf'] > 1) {
			throw QueryParseError::create('You cannot set rf greater than 1 when creating single node sharded table.');
		}
	}

	/**
	 * Convert the current state into array
	 * that we use for args in event
	 * @return array{
	 * table:array{cluster:string,name:string,structure:string,extra:string},
	 * replicationFactor:int,
	 * shardCount:int
	 * }
	 */
	public function toShardArgs(): array {
		return [
			'table' => [
				'cluster' => $this->cluster,
				'name' => $this->table,
				'structure' => $this->structure,
				'extra' => $this->extra,
			],
			'replicationFactor' => (int)($this->options['rf'] ?? 1),
			'shardCount' => (int)($this->options['shards'] ?? 2),
		];
	}
}
