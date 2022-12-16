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

namespace PrestaShop\PrestaShop\Core\Util\String;

use Transliterator;

/**
 * This class defines reusable methods for strings modifications.
 */
final class StringModifier implements StringModifierInterface
{
    /**
     * @var Transliterator|null
     */
    private $transliterator;

    /**
     * {@inheritdoc}
     */
    public function splitByCamelCase($string)
    {
        $regex = '/(?)(?<=[a-z])(?=[A-Z]) | (?<=[A-Z])(?=[A-Z][a-z])/x';

        $splitString = preg_split($regex, $string);

        return implode(' ', $splitString);
    }

    /**
     * {@inheritdoc}
     */
    public function cutEnd(string $string, int $expectedLength): string
    {
        $length = strlen($string);

        if ($length > $expectedLength) {
            // cut symbols difference from the end of the string
            $string = substr($string, 0, $expectedLength - $length);
        }

        return $string;
    }

    /**
     * Return a friendly url made from the provided string
     * If the mbstring library is available, the output is the same as the js function of the same name.
     *
     * @param string $string
     *
     * @return string
     */
    public function str2url(string $string): string
    {
        $return_str = trim($string);
        $return_str = mb_strtolower($return_str, 'UTF-8');

        $return_str = $this->replaceAccentedChars($return_str);
        $return_str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\p{L}]/u', '', $return_str);
        $return_str = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $return_str);

        return str_replace([' ', '/'], '-', $return_str);
    }

    /**
     * Replace all accented chars by their equivalent non-accented chars.
     *
     * @param string $string
     *
     * @return string
     */
    public function replaceAccentedChars(string $string): string
    {
        if (null === $this->transliterator) {
            $this->transliterator = Transliterator::create('Any-Latin; Latin-ASCII');
        }

        return $this->transliterator->transliterate($string);
    }

    /**
     * Adds a prefix to a last element of provided field path.
     * Field path elements expected to be separated by "[]".
     * It is used, for example, to prefix a form field with a "modify_all_shops_"
     * to avoid doing it manual for every multiShop field form.
     *
     * If it doesn't find any matches, it will return the same string $fieldPath.
     *
     * E.g. if you provide $fieldPath = '[stock][delta_quantity][delta]' and $prefix = 'modify_all_shops_'
     * then the result will be '[stock][delta_quantity][modify_all_shops_delta]'
     *
     * @param string $fieldPath
     * @param string $prefix
     *
     * @return string
     */
    public function prefixFieldPath(string $fieldPath, string $prefix): string
    {
        /*
         * Finds all matches between angle brackets.
         * E.g. for field "[foo][bar]" it will return array of
         *
         *    [
         *        ['[foo]','[bar]'],
         *        ['foo', 'bar']],
         *    ]
         */
        preg_match_all('/\\[(.*?)\\]/', $fieldPath, $matches);

        if (empty($matches[1])) {
            return $fieldPath;
        }

        $prefixedFieldName = '';
        $lastIndex = count($matches[1]) - 1;
        foreach ($matches[1] as $index => $subFieldName) {
            if ($index !== $lastIndex) {
                // It is not the last field, then just rebuild the field name as it was and continue searching for the last one
                $prefixedFieldName .= sprintf('[%s]', $subFieldName);

                continue;
            }

            // it is the last field, so we prefix it with provided $prefix inside the angle brackets
            $prefixedFieldName .= sprintf('[%s%s]', $prefix, $subFieldName);
        }

        return $prefixedFieldName;
    }
}
