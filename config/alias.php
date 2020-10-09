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

use Symfony\Component\VarDumper\VarDumper;

if (!function_exists('dump')) {
    function dump($var)
    {
        foreach (func_get_args() as $var) {
            VarDumper::dump($var);
        }
    }
}

/**
 * Sanitize data which will be injected into SQL query
 *
 * @param string $string SQL data which will be injected into SQL query
 * @param bool $htmlOK Does data contain HTML code ? (optional)
 * @return string Sanitized data
 */
function pSQL($string, $htmlOK = false)
{
    return Db::getInstance()->escape($string, $htmlOK);
}

function bqSQL($string)
{
    return str_replace('`', '\`', pSQL($string));
}

function displayFatalError()
{
    $error = null;
    if (function_exists('error_get_last')) {
        $error = error_get_last();
    }
    if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR))) {
        echo '[PrestaShop] Fatal error in module file: '.$error['file'].':'.$error['line'].'<br />'.$error['message'];
    }
}
