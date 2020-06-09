<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
class Profiler
{
    const COLOR_WARNING = '#EF8B00';
    const COLOR_SUCCESS = 'green';
    const COLOR_ERROR = 'red';

    const MIB_BYTES = 1048576;

    protected $hooksPerfs = [];
    protected $modulesPerfs = [];
    protected $profiler = [];

    protected $totalFilesize = 0;
    protected $totalGlobalVarSize = 0;
    protected $totalQueryTime = 0;
    protected $totalModulesTime = 0;
    protected $totalModulesMemory;
    protected $startTime = 0;

    protected static $instance = null;

    private function __construct()
    {
        $this->startTime = microtime(true);
    }

    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function sortByQueryTime($a, $b)
    {
        if ($a['time'] == $b['time']) {
            return 0;
        }

        return ($a['time'] > $b['time']) ? -1 : 1;
    }

    public function stamp($block)
    {
        $this->profiler[] = [
            'block' => $block,
            'memory_usage' => memory_get_usage(),
            'peak_memory_usage' => memory_get_peak_usage(),
            'time' => microtime(true),
        ];
    }

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

    private function getVarData($var)
    {
        if (is_object($var)) {
            return $var;
        }

        return (string) $var;
    }

    public function interceptHook($hookName, array $params)
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
    }

    public function interceptModule(array $params)
    {
        $this->modulesPerfs[] = $params;
        $this->totalModulesTime += $params['time'];
        $this->totalModulesMemory += $params['memory'];
    }

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
                if (stristr($explain[0]['Extra'], 'filesort')) {
                    $queryRow['filesort'] = true;
                }

                foreach ($explain as $row) {
                    $queryRow['rows'] *= $row['rows'];
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
                'totalFileSize' => $this->totalFilesize / static::MIB_BYTES,
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
                'totalModulesTime' => $this->totalModulesTime,
                'totalModulesMemory' => $this->totalModulesMemory,
            ],
        ];
    }
}
