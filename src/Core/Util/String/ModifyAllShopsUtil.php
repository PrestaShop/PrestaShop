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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\String;

class ModifyAllShopsUtil
{
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
     * @param string $allShopsPrefix
     *
     * @return string
     */
    public static function prefixFieldPathWithAllShops(string $fieldPath, string $allShopsPrefix): string
    {
        /*
         * Finds all matches between angle brackets.
         * E.g. for field "[foo][bar]" it will return array of
         *
         *    [
         *        ['[foo]','[bar]'],
         *        ['foo', 'bar'],
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
            $prefixedFieldName .= sprintf('[%s%s]', $allShopsPrefix, $subFieldName);
        }

        return $prefixedFieldName;
    }
}
