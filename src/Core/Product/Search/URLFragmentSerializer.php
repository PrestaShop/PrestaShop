<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Core\Product\Search;

class URLFragmentSerializer
{
    private function serializeListOfStrings($separator, $escape, array $list)
    {
        return implode($separator, array_map(function ($item) use ($separator, $escape) {
            return str_replace($separator, $escape.$separator, $item);
        }, $list));
    }

    private function unserializeListOfStrings($separator, $escape, $str)
    {
        $list = [];
        $currentString = '';
        $escaping = false;

        // get UTF-8 chars, inspired from http://stackoverflow.com/questions/9438158/split-utf8-string-into-array-of-chars
        $arr = [];
        preg_match_all('/./u', $str, $arr);
        $chars = $arr[0];

        foreach ($chars as $char) {
            if ($escaping) {
                if ($char === $separator || $char === $escape) {
                    $currentString .= $char;
                } else {
                    $list[] = $currentString;
                    $currentString = $char;
                }
                $escaping = false;
            } else {
                if ($char === $escape) {
                    $escaping = true;
                } elseif ($char === $separator) {
                    $list[] = $currentString;
                    $currentString = '';
                } else {
                    $currentString .= $char;
                }
            }
        }

        if ($escaping) {
            $currentString .= $escape;
        }

        if ('' !== $currentString) {
            $list[] = $currentString;
        }

        return $list;
    }

    public function serialize(array $fragment)
    {
        $parts = [];
        foreach ($fragment as $key => $values) {
            array_unshift($values, $key);
            $parts[] = $this->serializeListOfStrings('-', '-', $values);
        }

        return $this->serializeListOfStrings('/', '/', $parts);
    }

    public function unserialize($str)
    {
        $fragment = [];
        $parts = $this->unserializeListOfStrings('/', '/', $str);
        foreach ($parts as $part) {
            $values = $this->unserializeListOfStrings('-', '-', $part);
            $key = array_shift($values);
            $fragment[$key] = $values;
        }

        return $fragment;
    }
}
