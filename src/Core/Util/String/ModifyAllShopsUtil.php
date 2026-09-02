<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
