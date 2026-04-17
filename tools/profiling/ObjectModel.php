<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
abstract class ObjectModel extends ObjectModelCore
{
    public static $debug_list = [];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);

        $classname = get_class($this);
        if (!isset(self::$debug_list[$classname])) {
            self::$debug_list[$classname] = [];
        }

        $class_list = ['ObjectModel', 'ObjectModelCore', $classname, $classname . 'Core'];
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($backtrace as $trace_id => $row) {
            if (!isset($backtrace[$trace_id]['class']) || !in_array($backtrace[$trace_id]['class'], $class_list)) {
                break;
            }
        }
        if (!isset($trace_id)) {
            return;
        }
        --$trace_id;

        self::$debug_list[$classname][] = [
            'file' => $backtrace[$trace_id]['file'] ?? '',
            'line' => $backtrace[$trace_id]['line'] ?? 0,
            'function' => $backtrace[$trace_id]['function'] ?? 0,
            'id' => $id,
        ];
    }
}
