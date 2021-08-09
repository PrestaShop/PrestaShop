<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $this->startTime = microtime(true);
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
     * @return string|object
     */
    private function getVarData($var)
    {
        if (is_object($var)) {
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
            $this->totalFilesize += filesize($file);
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
        $queries = Db::getInstance()->queries;
        uasort($queries, [$this, 'sortByQueryTime']);
        foreach ($queries as $data) {
            $this->totalQueryTime += $data['time'];

            $queryRow = [
                'time' => $data['time'],
                'query' => $data['query'],
                'location' => str_replace('\\', '/', substr($data['stack'][0]['file'], strlen(_PS_ROOT_DIR_))) . ':' . $data['stack'][0]['line'],
                'filesort' => false,
                'rows' => 1,
                'group_by' => false,
                'stack' => [],
            ];

            if (preg_match('/^\s*select\s+/i', $data['query'])) {
                $explain = Db::getInstance()->executeS('explain ' . $data['query']);
                if (isset($explain[0]['Extra']) && stristr($explain[0]['Extra'], 'filesort')) {
                    $queryRow['filesort'] = true;
                }

                foreach ($explain as $row) {
                    $queryRow['rows'] *= (int) $row['rows'];
                }

                if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query'])) {
                    $queryRow['group_by'] = true;
                }
            }

            array_shift($data['stack']);
            foreach ($data['stack'] as $call) {
                $queryRow['stack'][] = str_replace('\\', '/', substr($call['file'], strlen(_PS_ROOT_DIR_))) . ':' . $call['line'];
            }

            $this->queries[] = $queryRow;
        }

        uasort(ObjectModel::$debug_list, function ($a, $b) { return (count($a) < count($b)) ? 1 : -1; });
        arsort(Db::getInstance()->tables);
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
                    function (&$res, $item) {
                        return $res + $item['time'];
                    },
                    0
                ),
                'total_memory' => array_reduce(
                    $perfs,
                    function (&$res, $item) {
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
        return [
            'summary' => [
                'loadTime' => $this->profiler[count($this->profiler) - 1]['time'] - $this->startTime,
                'queryTime' => round(1000 * $this->totalQueryTime),
                'nbQueries' => count($this->queries),
                'peakMemoryUsage' => $this->profiler[count($this->profiler) - 1]['peak_memory_usage'],
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
            'doublesQueries' => Db::getInstance()->uniqQueries,
            'tableStress' => Db::getInstance()->tables,
            'objectmodel' => ObjectModel::$debug_list,
            'files' => get_included_files(),
        ];
    }
}
