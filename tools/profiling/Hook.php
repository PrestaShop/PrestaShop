<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

class Hook extends HookCore
{
    public static function coreCallHook($module, $method, $params)
    {
        $timeStart = microtime(true);
        $memoryStart = memory_get_usage();

        $result = parent::coreCallHook($module, $method, $params);

        Profiler::getInstance()->interceptHook(
            substr($method, 4),
            [
                'module' => $module->name,
                'params' => $params,
                'time' => microtime(true) - $timeStart,
                'memory' => memory_get_usage() - $memoryStart,
            ]
        );

        Profiler::getInstance()->interceptModule(
            [
                'module' => $module->name,
                'method' => $method,
                'time' => microtime(true) - $timeStart,
                'memory' => memory_get_usage() - $memoryStart,
            ]
        );

        return $result;
    }

    public static function coreRenderWidget($module, $registeredHookName, $params)
    {
        $timeStart = microtime(true);
        $memoryStart = memory_get_usage();

        $result = parent::coreRenderWidget($module, $registeredHookName, $params);

        /*
         * It's not a hook which has been triggered but
         * it's a widget
         */
        if (empty($registeredHookName)) {
            $registeredHookName = 'renderWidget';
        }

        Profiler::getInstance()->interceptHook(
            $registeredHookName,
            [
                'module' => $module->name . ' (widget)',
                'params' => $params,
                'time' => microtime(true) - $timeStart,
                'memory' => memory_get_usage() - $memoryStart,
            ]
        );

        Profiler::getInstance()->interceptModule(
            [
                'module' => $module->name,
                'method' => $registeredHookName,
                'time' => microtime(true) - $timeStart,
                'memory' => memory_get_usage() - $memoryStart,
            ]
        );

        return $result;
    }
}
