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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

@trigger_error(
    sprintf(
        '%s is deprecated since version 1.7.8.0 and will be removed in the next major version.',
        URLFragmentSerializer::class
    ),
    E_USER_DEPRECATED
);

/**
 * This class is a serializer for URL fragments.
 *
 * @deprecated since version 1.7.8 and will be removed in the next major version.
 */
class URLFragmentSerializer
{
    /**
     * @param array $fragment
     *
     * @return string
     */
    public function serialize(array $fragment)
    {
        $parts = [];
        foreach ($fragment as $key => $values) {
            array_unshift($values, $key);
            $parts[] = $this->serializeListOfStrings('-', '-', $values);
        }

        return $this->serializeListOfStrings('/', '/', $parts);
    }

    /**
     * @param string $string
     *
     * @return array
     */
    public function unserialize($string)
    {
        $fragment = [];
        $parts = $this->unserializeListOfStrings('/', '/', $string);
        foreach ($parts as $part) {
            $values = $this->unserializeListOfStrings('-', '-', $part);
            $key = array_shift($values);
            $fragment[$key] = $values;
        }

        return $fragment;
    }

    /**
     * @param string $separator the string separator
     * @param string $escape the string escape
     * @param array $list
     *
     * @return string
     */
    private function serializeListOfStrings($separator, $escape, array $list)
    {
        return implode($separator, array_map(function ($item) use ($separator, $escape) {
            return str_replace($separator, $escape . $separator, $item);
        }, $list));
    }

    /**
     * @param string $separator the string separator
     * @param string $escape the string escape
     * @param string $string the UTF8 string
     *
     * @return array
     */
    private function unserializeListOfStrings($separator, $escape, $string)
    {
        $list = [];
        $currentString = '';
        $escaping = false;

        // get UTF-8 chars, inspired from http://stackoverflow.com/questions/9438158/split-utf8-string-into-array-of-chars
        $arrayOfCharacters = [];
        preg_match_all('/./u', $string, $arrayOfCharacters);
        $characters = $arrayOfCharacters[0];

        foreach ($characters as $character) {
            if ($escaping) {
                if ($character === $separator || $character === $escape) {
                    $currentString .= $character;
                } else {
                    $list[] = $currentString;
                    $currentString = $character;
                }
                $escaping = false;
            } else {
                if ($character === $escape) {
                    $escaping = true;
                } elseif ($character === $separator) {
                    $list[] = $currentString;
                    $currentString = '';
                } else {
                    $currentString .= $character;
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
}
