<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!function_exists('array_pluck')) {
    /**
     * Pluck an array of values from an array.
     *
     * @param  array   $array
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    function array_pluck($array, $value, $key = null)
    {
        $results = array();

        list($value, $key) = explode_pluck_parameters($value, $key);

        foreach ($array as $item) {
            $item_value = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $item_value;
            } else {
                $item_key = data_get($item, $key);

                $results[$item_key] = $item_value;
            }
        }

        return $results;
    }
}

if (!function_exists('explode_pluck_parameters')) {
    /**
     * Explode the "value" and "key" arguments passed to "array_pluck".
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    function explode_pluck_parameters($value, $key)
    {
        $value = is_array($value) ? $value : explode('.', $value);

        $key = (is_null($key) || is_array($key)) ? $key : explode('.', $key);

        return array($value, $key);
    }
}
