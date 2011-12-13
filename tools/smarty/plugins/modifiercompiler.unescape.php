<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty unescape modifier plugin
 *
 * Type:     modifier<br>
 * Name:     unescape<br>
 * Purpose:  unescape html entities
 *
 * @author Rodney Rehm
 * @param array $params parameters
 * @return string with compiled code
 */
function smarty_modifiercompiler_unescape($params, $compiler)
{
    if (!isset($params[1])) {
        $params[1] = 'html';
    }
    if (!isset($params[2])) {
        $params[2] = "SMARTY_RESOURCE_CHAR_SET";
    } else {
        $params[2] = "'" . $params[2] . "'";
    }

    switch (trim($params[1], '"\'')) {
        case 'entity':
            return 'mb_convert_encoding(' . $params[0] . ', ' . $params[2] . ', \'HTML-ENTITIES\')';
        case 'htmlall':
            if (SMARTY_MBSTRING /* ^phpunit */&&empty($_SERVER['SMARTY_PHPUNIT_DISABLE_MBSTRING'])/* phpunit$ */) {
                return 'mb_convert_encoding(' . $params[0] . ', ' . $params[2] . ', \'HTML-ENTITIES\')';
            }
            return 'html_entity_decode(' . $params[0] . ', ENT_QUOTES, ' . $params[2] . ')';

        case 'html':
            return 'htmlspecialchars_decode(' . $params[0] . ', ENT_QUOTES)';

        default:
            return $params[0];
    }
}

?>