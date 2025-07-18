<?php
/**
 * TableProcessor.php
 *
 * This file implements the processor for the TABLE statements.
 *
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2014 Justin Swanhart and André Rothe
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    André Rothe <andre.rothe@phosco.info>
 * @copyright 2010-2014 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id$
 *
 */

namespace PHPSQLParser\processors;

use PHPSQLParser\utils\ExpressionType;

/**
 * This class processes the TABLE statements.
 *
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 */
class TableProcessor extends AbstractProcessor
{

    protected function getReservedType($token)
    {
        return array('expr_type' => ExpressionType::RESERVED, 'base_expr' => $token);
    }

    protected function getConstantType($token)
    {
        return array('expr_type' => ExpressionType::CONSTANT, 'base_expr' => $token);
    }

    protected function getOperatorType($token)
    {
        return array('expr_type' => ExpressionType::OPERATOR, 'base_expr' => $token);
    }

    protected function processPartitionOptions($tokens)
    {
        $processor = new PartitionOptionsProcessor($this->options);
        return $processor->process($tokens);
    }

    protected function processCreateDefinition($tokens)
    {
        $processor = new CreateDefinitionProcessor($this->options);
        return $processor->process($tokens);
    }

    protected function clear(&$expr, &$base_expr, &$category)
    {
        $expr = array();
        $base_expr = '';
        $category = 'CREATE_DEF';
    }

    public function process($tokens)
    {

        $currCategory = 'TABLE_NAME';
        $result = array('base_expr' => false, 'name' => false, 'no_quotes' => false, 'create-def' => false,
            'options' => array(), 'like' => false, 'select-option' => false);
        $expr = array();
        $base_expr = '';
        $skip = 0;

        foreach ($tokens as $tokenKey => $token) {
            $trim = trim($token);
            $base_expr .= $token;

            if ($skip > 0) {
                $skip--;
                continue;
            }

            if ($skip < 0) {
                break;
            }

            if ($trim === '') {
                continue;
            }

            $upper = strtoupper($trim);
            switch ($upper) {

                case ',':
                    // it is possible to separate the table options with comma!
                    if ($prevCategory === 'CREATE_DEF') {
                        $last = array_pop($result['options']);
                        $last['delim'] = ',';
                        $result['options'][] = $last;
                        $base_expr = '';
                    }
                    continue 2;

                case 'UNION':
                    if ($prevCategory === 'CREATE_DEF') {
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'UNION';
                        continue 2;
                    }
                    break;

                case 'LIKE':
                    // like without parenthesis
                    if ($prevCategory === 'TABLE_NAME') {
                        $currCategory = $upper;
                        continue 2;
                    }
                    break;

                case '=':
                    // the optional operator
                    if ($prevCategory === 'TABLE_OPTION') {
                        $expr[] = $this->getOperatorType($trim);
                        continue 2; // don't change the category
                    }
                    break;

                case 'CHARACTER':
                    if ($prevCategory === 'CREATE_DEF') {
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'TABLE_OPTION';
                    }
                    if ($prevCategory === 'TABLE_OPTION') {
                        // add it to the previous DEFAULT
                        $expr[] = $this->getReservedType($trim);
                        continue 2;
                    }
                    break;

                case 'SET':
                case 'CHARSET':
                    if ($prevCategory === 'TABLE_OPTION') {
                        // add it to a previous CHARACTER
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'CHARSET';
                        continue 2;
                    }
                    break;

                case 'COLLATE':
                    if ($prevCategory === 'TABLE_OPTION' || $prevCategory === 'CREATE_DEF') {
                        // add it to the previous DEFAULT
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'COLLATE';
                        continue 2;
                    }
                    break;

                case 'DIRECTORY':
                    if ($currCategory === 'INDEX_DIRECTORY' || $currCategory === 'DATA_DIRECTORY') {
                        // after INDEX or DATA
                        $expr[] = $this->getReservedType($trim);
                        continue 2;
                    }
                    break;

                case 'INDEX':
                    if ($prevCategory === 'CREATE_DEF') {
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'INDEX_DIRECTORY';
                        continue 2;
                    }
                    break;

                case 'WITH':
                    if ($prevCategory === 'CREATE_DEF') {
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'WITH';
                        continue 2;
                    }
                    break;
                case 'DATA':
                    if ($prevCategory === 'CREATE_DEF') {
                        $expr[] = $this->getReservedType($trim);

                        if ($currCategory != 'WITH'){
                            $currCategory = 'DATA_DIRECTORY';
                        }else{
                            $result['options'][] = array('expr_type' => ExpressionType::EXPRESSION,
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $currCategory);
                        }
                        continue 2;
                    }
                    break;
                case 'INSERT_METHOD':
                case 'DELAY_KEY_WRITE':
                case 'ROW_FORMAT':
                case 'PASSWORD':
                case 'MAX_ROWS':
                case 'MIN_ROWS':
                case 'PACK_KEYS':
                case 'CHECKSUM':
                case 'CONNECTION':
                case 'AUTO_INCREMENT':
                case 'AVG_ROW_LENGTH':
                case 'ENGINE':
                case 'EXCEPTIONS':
                case 'WORDFORMS':
                case 'TYPE':
                case 'STATS_AUTO_RECALC':
                case 'STATS_PERSISTENT':
                case 'LOCAL':
                case 'AGENT':
                case 'KEY_BLOCK_SIZE':
                    if (in_array($prevCategory, ['CREATE_DEF', 'TABLE_NAME']) ) {
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = $prevCategory = 'TABLE_OPTION';
                        continue 2;
                    }
                    break;

                case 'DYNAMIC':
                case 'FIXED':
                case 'COMPRESSED':
                case 'REDUNDANT':
                case 'COMPACT':
                case 'NO':
                case 'FIRST':
                case 'LAST':
                case 'DEFAULT':
                    if ($prevCategory === 'CREATE_DEF') {
                        // DEFAULT before CHARACTER SET and COLLATE
                        $expr[] = $this->getReservedType($trim);
                        $currCategory = 'TABLE_OPTION';
                    }
                    if ($prevCategory === 'TABLE_OPTION') {
                        // all assignments with the keywords
                        $expr[] = $this->getReservedType($trim);
                        $result['options'][] = array('expr_type' => ExpressionType::EXPRESSION,
                            'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                        $this->clear($expr, $base_expr, $currCategory);
                    }
                    break;

                case 'IGNORE':
                case 'REPLACE':
                    $expr[] = $this->getReservedType($trim);
                    $result['select-option'] = array('base_expr' => trim($base_expr), 'duplicates' => $trim, 'as' => false,
                        'sub_tree' => $expr);
                    continue 2;

                case 'AS':
                    $expr[] = $this->getReservedType($trim);
                    if (!isset($result['select-option']['duplicates'])) {
                        $result['select-option']['duplicates'] = false;
                    }
                    $result['select-option']['as'] = true;
                    $result['select-option']['base_expr'] = trim($base_expr);
                    $result['select-option']['sub_tree'] = $expr;
                    continue 2;

                case 'PARTITION':
                    if ($prevCategory === 'CREATE_DEF') {
                        $part = $this->processPartitionOptions(array_slice($tokens, $tokenKey - 1, null, true));
                        $skip = $part['last-parsed'] - $tokenKey;
                        $result['partition-options'] = $part['partition-options'];
                        continue 2;
                    }
                    // else
                    break;

                default:
                    switch ($currCategory) {

                        case 'CHARSET':
                            // the charset name
                            $expr[] = $this->getConstantType($trim);
                            $result['options'][] = array('expr_type' => ExpressionType::CHARSET,
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $currCategory);
                            break;

                        case 'COLLATE':
                            // the collate name
                            $expr[] = $this->getConstantType($trim);
                            $result['options'][] = array('expr_type' => ExpressionType::COLLATE,
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $currCategory);
                            break;

                        case 'DATA_DIRECTORY':
                            // we have the directory name
                            $expr[] = $this->getConstantType($trim);
                            $result['options'][] = array('expr_type' => ExpressionType::DIRECTORY, 'kind' => 'DATA',
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $prevCategory);
                            continue 3;

                        case 'INDEX_DIRECTORY':
                            // we have the directory name
                            $expr[] = $this->getConstantType($trim);
                            $result['options'][] = array('expr_type' => ExpressionType::DIRECTORY, 'kind' => 'INDEX',
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $prevCategory);
                            continue 3;

                        case 'TABLE_NAME':
                            $result['base_expr'] = $result['name'] = $trim;
                            $result['no_quotes'] = $this->revokeQuotation($trim);
                            $this->clear($expr, $base_expr, $prevCategory);
                            break;

                        case 'LIKE':
                            $result['like'] = array('expr_type' => ExpressionType::TABLE, 'table' => $trim,
                                'base_expr' => $trim, 'no_quotes' => $this->revokeQuotation($trim));
                            $this->clear($expr, $base_expr, $currCategory);
                            break;

                        case '':
                            // after table name
                            if ($prevCategory === 'TABLE_NAME' && $upper[0] === '(' && substr($upper, -1) === ')') {
                                $unparsed = $this->splitSQLIntoTokens($this->removeParenthesisFromStart($trim));
                                $coldef = $this->processCreateDefinition($unparsed);
                                $result['create-def'] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION,
                                    'base_expr' => $base_expr, 'sub_tree' => $coldef['create-def']);
                                $expr = array();
                                $base_expr = '';
                                $currCategory = 'CREATE_DEF';
                            }
                            break;

                        case 'UNION':
                            // TODO: this token starts and ends with parenthesis
                            // and contains a list of table names (comma-separated)
                            // split the token and add the list as subtree
                            // we must change the DefaultProcessor

                            $unparsed = $this->splitSQLIntoTokens($this->removeParenthesisFromStart($trim));
                            $expr[] = array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => $trim,
                                'sub_tree' => '***TODO***');
                            $result['options'][] = array('expr_type' => ExpressionType::UNION, 'base_expr' => trim($base_expr),
                                'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $currCategory);
                            break;

                        default:
                            // strings and numeric constants
                            $expr[] = $this->getConstantType($trim);
                            $result['options'][] = array('expr_type' => ExpressionType::EXPRESSION,
                                'base_expr' => trim($base_expr), 'delim' => ' ', 'sub_tree' => $expr);
                            $this->clear($expr, $base_expr, $currCategory);
                            break;
                    }
                    break;
            }

            $prevCategory = $currCategory;
            $currCategory = '';
        }

        if ($result['like'] === false) {
            unset($result['like']);
        }
        if ($result['select-option'] === false) {
            unset($result['select-option']);
        }
        if ($result['options'] === array()) {
            $result['options'] = false;
        }

        return $result;
    }
}

?>