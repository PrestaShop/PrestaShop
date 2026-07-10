<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

class Profiler
{
    /** @var array */
    protected $hooksPerfs = [];

    /** @var array */
    protected $modulesPerfs = [];

    /** @var array */
    protected $profiler = [];

    /** @var array */
    protected $globalVarSize = [];

    /** @var array */
    protected $queries = [];

    /** @var int */
    protected $totalFilesize = 0;

    /** @var int */
    protected $totalGlobalVarSize = 0;

    /** @var float */
    protected $totalQueryTime = 0;

    /** @var float */
    protected $totalModulesTime = 0;

    /** @var int */
    protected $totalModulesMemory = 0;

    /** @var float */
    protected $totalHooksTime = 0;

    /** @var int */
    protected $totalHooksMemory = 0;

    /** @var float */
    protected $startTime = 0;

    /** @var int */
    protected $totalCacheSize = 0;

    protected static $instance = null;

    private function __construct()
    {
        global $start_time;
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $this->startTime = (float) $_SERVER['REQUEST_TIME_FLOAT'];
        } elseif (!empty($start_time)) {
            $this->startTime = $start_time;
        } else {
            $this->startTime = microtime(true);
        }
    }

    /**
     * Return profiler instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Sort array by query time
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public function sortByQueryTime(array $a, array $b): int
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }

        return ($a['time'] > $b['time']) ? -1 : 1;
    }

    /**
     * Stamp the profiling
     *
     * @param string $block
     */
    public function stamp(string $block)
    {
        $this->profiler[] = [
            'block' => $block,
            'memory_usage' => memory_get_usage(),
            'peak_memory_usage' => memory_get_peak_usage(),
            'time' => microtime(true),
        ];
    }

    /**
     * Get var size
     *
     * @param mixed $var
     */
    private function getVarSize($var)
    {
        $start_memory = memory_get_usage();

        try {
            $tmp = Tools::unSerialize(serialize($var));
        } catch (Exception $e) {
            $tmp = $this->getVarData($var);
        }

        $size = memory_get_usage() - $start_memory;

        return $size;
    }

    /**
     * Get var data
     *
     * @param mixed $var
     *
     * @return string|object|array
     */
    private function getVarData($var)
    {
        if (is_object($var)) {
            return $var;
        }

        if (is_array($var)) {
            return $var;
        }

        return (string) $var;
    }

    /**
     * Intercept hook and register its data
     *
     * @param string $hookName
     * @param array $params
     */
    public function interceptHook(string $hookName, array $params)
    {
        if (empty($this->hooksPerfs[$hookName])) {
            $this->hooksPerfs[$hookName] = [
                'time' => 0,
                'memory' => 0,
                'modules' => [],
            ];
        }

        $this->hooksPerfs[$hookName]['time'] += $params['time'];
        $this->hooksPerfs[$hookName]['memory'] += $params['memory'];
        $this->hooksPerfs[$hookName]['modules'][] = $params;
        $this->totalHooksMemory += $params['memory'];
        $this->totalHooksTime += $params['time'];
    }

    /**
     * Intercept module
     *
     * @param array $params
     */
    public function interceptModule(array $params)
    {
        $this->modulesPerfs[$params['module']][] = $params;

        $this->totalModulesTime += $params['time'];
        $this->totalModulesMemory += $params['memory'];
    }

    /**
     * Process all data such as Global vars and
     * database queries
     */
    public function processData()
    {
        // Including a lot of files uses memory
        foreach (get_included_files() as $file) {
            if (file_exists($file)) {
                $this->totalFilesize += filesize($file);
            }
        }

        foreach ($GLOBALS as $key => $value) {
            if ($key === 'GLOBALS') {
                continue;
            }
            $this->totalGlobalVarSize += ($size = $this->getVarSize($value));

            if ($size > 1024) {
                $this->globalVarSize[$key] = round($size / 1024);
            }
        }

        arsort($this->globalVarSize);

        $cache = Cache::retrieveAll();
        $this->totalCacheSize = $this->getVarSize($cache);

        // Sum querying time
        /* @phpstan-ignore-next-line */
        $queries = Db::getInstance()->queries;
        uasort($queries, [$this, 'sortByQueryTime']);
        foreach ($queries as $id => $data) {
            $this->totalQueryTime += $data['time'];

            $queryRow = [
                'time' => $data['time'],
                'query' => $data['query'],
                'location' => str_replace('\\', '/', substr($data['stack'][0]['file'], strlen(_PS_ROOT_DIR_))) . ':' . $data['stack'][0]['line'],
                'filesort' => false,
                'rows' => 1,
                'group_by' => false,
                'stack' => [],
                'id' => $id,
            ];

            if (preg_match('/^\s*select\s+/i', $data['query'])) {
                $explain = Db::getInstance()->executeS('explain ' . $data['query']);
                if (isset($explain[0]['Extra']) && stristr($explain[0]['Extra'], 'filesort')) {
                    $queryRow['filesort'] = true;
                }

                if (is_array($explain)) {
                    foreach ($explain as $row) {
                        $queryRow['rows'] *= (int) $row['rows'];
                    }
                } else {
                    $queryRow['rows'] = 'N/A';
                }

                if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query'])) {
                    $queryRow['group_by'] = true;
                }
            }

            array_shift($data['stack']);
            foreach ($data['stack'] as $call) {
                $queryRow['stack'][] = str_replace('\\', '/', substr($call['file'], strlen(_PS_ROOT_DIR_))) . ':' . $call['line'] . ' (' . $call['function'] . ')';
            }

            $this->queries[] = $queryRow;
        }

        /* @phpstan-ignore-next-line */
        uasort(ObjectModel::$debug_list, function ($a, $b) { return (count($a) < count($b)) ? 1 : -1; });
        /* @phpstan-ignore-next-line */
        arsort(Db::getInstance()->tables);
        /* @phpstan-ignore-next-line */
        arsort(Db::getInstance()->uniqQueries);
        uasort($this->hooksPerfs, [$this, 'sortByQueryTime']);
    }

    /**
     * Format performance details for modules
     *
     * @return array
     */
    public function getFormattedModulePerfs(): array
    {
        $formattedOutput = [];
        foreach ($this->modulesPerfs as $moduleName => $perfs) {
            $formattedOutput[$moduleName] = [
                'total_time' => array_reduce(
                    $perfs,
                    function ($res, $item) {
                        return $res + $item['time'];
                    },
                    0
                ),
                'total_memory' => array_reduce(
                    $perfs,
                    function ($res, $item) {
                        return $res + $item['memory'];
                    },
                    0
                ),
                'details' => $perfs,
            ];
        }

        return $formattedOutput;
    }

    /**
     * Prepare and return smarty variables
     *
     * @return array
     */
    public function getSmartyVariables(): array
    {
        /* @phpstan-ignore-next-line */
        $doublesQueries = Db::getInstance()->uniqQueries;
        /* @phpstan-ignore-next-line */
        $tableStress = Db::getInstance()->tables;

        return [
            'summary' => [
                'loadTime' => empty($this->profiler) ? null : $this->profiler[count($this->profiler) - 1]['time'] - $this->startTime,
                'queryTime' => round(1000 * $this->totalQueryTime),
                'nbQueries' => count($this->queries),
                'peakMemoryUsage' => empty($this->profiler) ? null : $this->profiler[count($this->profiler) - 1]['peak_memory_usage'],
                'globalVarSize' => $this->globalVarSize,
                'includedFiles' => count(get_included_files()),
                'totalFileSize' => $this->totalFilesize,
                'totalCacheSize' => $this->totalCacheSize,
                'totalGlobalVarSize' => $this->totalGlobalVarSize,
            ],
            'configuration' => [
                'psVersion' => _PS_VERSION_,
                'phpVersion' => PHP_VERSION,
                'mysqlVersion' => Db::getInstance()->getVersion(),
                'memoryLimit' => ini_get('memory_limit'),
                'maxExecutionTime' => ini_get('max_execution_time'),
                'smartyCache' => Configuration::get('PS_SMARTY_CACHE'),
                'smartyCompilation' => Configuration::get('PS_SMARTY_FORCE_COMPILE'),
            ],
            'run' => [
                'startTime' => $this->startTime,
                'profiler' => $this->profiler,
            ],
            'hooks' => [
                'perfs' => $this->hooksPerfs,
                'totalHooksTime' => $this->totalHooksTime,
                'totalHooksMemory' => $this->totalHooksMemory,
            ],
            'modules' => [
                'perfs' => $this->getFormattedModulePerfs(),
                'totalHooksTime' => $this->totalModulesTime,
                'totalHooksMemory' => $this->totalModulesMemory,
            ],
            'stopwatchQueries' => $this->queries,
            'doublesQueries' => $doublesQueries,
            'tableStress' => $tableStress,
            'objectmodel' => ObjectModel::$debug_list,
            'files' => get_included_files(),
        ];
    }

    /**
     * Build a Markdown report from the collected profiling data.
     *
     * @return string
     */
    public function getMarkdownReport(): string
    {
        $vars = $this->getSmartyVariables();
        $sanitizeRow = function ($value) {
            return str_replace(['|', "\r", "\n"], [' ', ' ', ' '], (string) $value);
        };

        $md = "# PrestaShop Profiling Report\n\n";
        $md .= '_Generated: ' . date('Y-m-d H:i:s') . "_\n\n";

        $md .= "## Summary\n\n";
        $md .= '- Load Time: ' . round((float) $vars['summary']['loadTime'], 4) . " s\n";
        $md .= '- Querying Time: ' . $vars['summary']['queryTime'] . " ms\n";
        $md .= '- Queries: ' . $vars['summary']['nbQueries'] . "\n";
        $md .= '- Peak Memory Usage: ' . $vars['summary']['peakMemoryUsage'] . " bytes\n";
        $md .= '- Included Files: ' . $vars['summary']['includedFiles'] . ' (' . $vars['summary']['totalFileSize'] . " bytes)\n";
        $md .= '- PrestaShop Cache: ' . $vars['summary']['totalCacheSize'] . " bytes\n";
        $md .= '- Global vars: ' . $vars['summary']['totalGlobalVarSize'] . " bytes\n";
        foreach ($vars['summary']['globalVarSize'] as $global => $size) {
            $md .= '  - $' . $global . ': ' . $size . " Kb\n";
        }
        $md .= "\n";

        $md .= "## Configuration\n\n";
        foreach ($vars['configuration'] as $key => $value) {
            $md .= '- ' . $key . ': ' . $value . "\n";
        }
        $md .= "\n";

        $md .= "## Hooks\n\n";
        foreach ($vars['hooks']['perfs'] as $hookName => $perf) {
            $md .= '### ' . $sanitizeRow($hookName) . ' — ' . round($perf['time'] * 1000, 2) . ' ms, ' . $perf['memory'] . " bytes\n\n";
            $md .= "| Module | Time (ms) | Memory (bytes) |\n|---|---|---|\n";
            foreach ($perf['modules'] as $moduleCall) {
                $md .= '| ' . $sanitizeRow($moduleCall['module']) . ' | ' . round($moduleCall['time'] * 1000, 2) . ' | ' . $moduleCall['memory'] . " |\n";
            }
            $md .= "\n";
        }

        $md .= "## Modules\n\n";
        foreach ($vars['modules']['perfs'] as $moduleName => $perf) {
            $md .= '### ' . $sanitizeRow($moduleName) . ' — ' . round($perf['total_time'] * 1000, 2) . ' ms, ' . $perf['total_memory'] . " bytes\n\n";
            $md .= "| Method | Time (ms) | Memory (bytes) |\n|---|---|---|\n";
            foreach ($perf['details'] as $call) {
                $md .= '| ' . $sanitizeRow($call['method']) . ' | ' . round($call['time'] * 1000, 2) . ' | ' . $call['memory'] . " |\n";
            }
            $md .= "\n";
        }

        $md .= "## Stopwatch queries\n\n";
        foreach ($vars['stopwatchQueries'] as $query) {
            $md .= '### #' . $query['id'] . ' — ' . round($query['time'] * 1000, 2) . ' ms — ' . $sanitizeRow($query['location']) . "\n\n";
            $md .= '```sql' . "\n" . trim($query['query']) . "\n" . '```' . "\n\n";
            $md .= '- Rows: ' . $query['rows'] . "\n";
            $md .= '- Filesort: ' . ($query['filesort'] ? 'yes' : 'no') . "\n";
            $md .= '- Group by: ' . ($query['group_by'] ? 'yes' : 'no') . "\n";
            if (!empty($query['stack'])) {
                $md .= "- Stack:\n";
                foreach ($query['stack'] as $frame) {
                    $md .= '  - ' . $sanitizeRow($frame) . "\n";
                }
            }
            $md .= "\n";
        }

        $md .= "## Duplicate queries\n\n";
        foreach ($vars['doublesQueries'] as $query => $count) {
            if ($count > 1) {
                $md .= '- (' . $count . 'x) `' . $sanitizeRow($query) . "`\n";
            }
        }
        $md .= "\n";

        $md .= "## Table stress\n\n| Table | Queries |\n|---|---|\n";
        foreach ($vars['tableStress'] as $table => $count) {
            $md .= '| ' . $sanitizeRow($table) . ' | ' . $count . " |\n";
        }
        $md .= "\n";

        $md .= "## ObjectModel instances\n\n| Class | Instances |\n|---|---|\n";
        foreach ($vars['objectmodel'] as $class => $instances) {
            $md .= '| ' . $sanitizeRow($class) . ' | ' . count($instances) . " |\n";
        }
        $md .= "\n";

        $md .= '## Included files (' . count($vars['files']) . ")\n\n";
        foreach ($vars['files'] as $file) {
            $md .= '- ' . str_replace('\\', '/', substr($file, strlen(_PS_ROOT_DIR_))) . "\n";
        }

        return $md;
    }
}
