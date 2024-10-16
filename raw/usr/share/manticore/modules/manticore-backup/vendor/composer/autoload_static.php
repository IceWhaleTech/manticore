<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite29b3dfa81552bb85cda8483381f43bd
{
    public static $files = array (
        'c465a1ccb744389e460a075281dda801' => __DIR__ . '/../..' . '/src/func.php',
    );

    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OpenMetrics\\Exposition\\Text\\' => 28,
        ),
        'M' => 
        array (
            'Manticoresoftware\\Telemetry\\' => 28,
            'Manticoresearch\\Backup\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OpenMetrics\\Exposition\\Text\\' => 
        array (
            0 => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src',
        ),
        'Manticoresoftware\\Telemetry\\' => 
        array (
            0 => __DIR__ . '/..' . '/manticoresoftware/telemetry/src',
        ),
        'Manticoresearch\\Backup\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Manticoresearch\\Backup\\Exception\\ChecksumException' => __DIR__ . '/../..' . '/src/Exception/ChecksumException.php',
        'Manticoresearch\\Backup\\Exception\\InvalidPathException' => __DIR__ . '/../..' . '/src/Exception/InvalidPathException.php',
        'Manticoresearch\\Backup\\Exception\\SearchdException' => __DIR__ . '/../..' . '/src/Exception/SearchdException.php',
        'Manticoresearch\\Backup\\Lib\\FileSortingIterator' => __DIR__ . '/../..' . '/src/Lib/FileSortingIterator.php',
        'Manticoresearch\\Backup\\Lib\\FileStorage' => __DIR__ . '/../..' . '/src/Lib/FileStorage.php',
        'Manticoresearch\\Backup\\Lib\\LogLevel' => __DIR__ . '/../..' . '/src/Lib/LogLevel.php',
        'Manticoresearch\\Backup\\Lib\\ManticoreBackup' => __DIR__ . '/../..' . '/src/Lib/ManticoreBackup.php',
        'Manticoresearch\\Backup\\Lib\\ManticoreClient' => __DIR__ . '/../..' . '/src/Lib/ManticoreClient.php',
        'Manticoresearch\\Backup\\Lib\\ManticoreConfig' => __DIR__ . '/../..' . '/src/Lib/ManticoreConfig.php',
        'Manticoresearch\\Backup\\Lib\\OS' => __DIR__ . '/../..' . '/src/Lib/OS.php',
        'Manticoresearch\\Backup\\Lib\\Searchd' => __DIR__ . '/../..' . '/src/Lib/Searchd.php',
        'Manticoresearch\\Backup\\Lib\\TextColor' => __DIR__ . '/../..' . '/src/Lib/TextColor.php',
        'Manticoresoftware\\Telemetry\\Metric' => __DIR__ . '/..' . '/manticoresoftware/telemetry/src/Metric.php',
        'OpenMetrics\\Exposition\\Text\\Collections\\AbstractMetricCollection' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Collections/AbstractMetricCollection.php',
        'OpenMetrics\\Exposition\\Text\\Collections\\CounterCollection' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Collections/CounterCollection.php',
        'OpenMetrics\\Exposition\\Text\\Collections\\GaugeCollection' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Collections/GaugeCollection.php',
        'OpenMetrics\\Exposition\\Text\\Collections\\LabelCollection' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Collections/LabelCollection.php',
        'OpenMetrics\\Exposition\\Text\\Exceptions\\InvalidArgumentException' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Exceptions/InvalidArgumentException.php',
        'OpenMetrics\\Exposition\\Text\\Exceptions\\LogicException' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Exceptions/LogicException.php',
        'OpenMetrics\\Exposition\\Text\\Exceptions\\RuntimeException' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Exceptions/RuntimeException.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\CollectsMetrics' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/CollectsMetrics.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\NamesMetric' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/NamesMetric.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\ProvidesMeasuredValue' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/ProvidesMeasuredValue.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\ProvidesMetricLines' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/ProvidesMetricLines.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\ProvidesNamedValue' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/ProvidesNamedValue.php',
        'OpenMetrics\\Exposition\\Text\\Interfaces\\ProvidesSampleString' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Interfaces/ProvidesSampleString.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Aggregations\\Count' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Aggregations/Count.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Aggregations\\Sum' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Aggregations/Sum.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Counter' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Counter.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Gauge' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Gauge.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Histogram' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Histogram.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Histogram\\Bucket' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Histogram/Bucket.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Histogram\\InfiniteBucket' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Histogram/InfiniteBucket.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Summary' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Summary.php',
        'OpenMetrics\\Exposition\\Text\\Metrics\\Summary\\Quantile' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Metrics/Summary/Quantile.php',
        'OpenMetrics\\Exposition\\Text\\Types\\Label' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Types/Label.php',
        'OpenMetrics\\Exposition\\Text\\Types\\MetricName' => __DIR__ . '/..' . '/manticoresoftware/openmetrics/src/Types/MetricName.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite29b3dfa81552bb85cda8483381f43bd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite29b3dfa81552bb85cda8483381f43bd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite29b3dfa81552bb85cda8483381f43bd::$classMap;

        }, null, ClassLoader::class);
    }
}
