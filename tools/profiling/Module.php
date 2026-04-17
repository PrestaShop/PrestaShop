<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

class Module extends ModuleCore
{
    protected static function coreLoadModule($moduleName)
    {
        $timeStart = microtime(true);
        $memoryStart = memory_get_usage();

        $result = parent::coreLoadModule($moduleName);

        Profiler::getInstance()->interceptModule(
            [
                'module' => $moduleName,
                'method' => '__construct',
                'time' => microtime(true) - $timeStart,
                'memory' => memory_get_usage() - $memoryStart,
            ]
        );

        return $result;
    }
}
