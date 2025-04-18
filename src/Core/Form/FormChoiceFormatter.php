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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form;

/**
 * Class that formats choices for Symfony forms.
 */
class FormChoiceFormatter
{
    /**
     * Returns a choice list in a format required for Symfony form.
     * Symfony form choice fields accept array with options, where the item name
     * is the key and item ID is the value.
     *
     * So, when there are two items with the same name, they get lost.
     * This will automatically mark duplicate options with their ID.
     *
     * @param array $rawChoices Raw array with data to build the options from
     * @param string $idKey Key name of the item IDs, id_carrier for example
     * @param string $nameKey Key name of the item NAMEs, carrier_name for example
     * @param bool $sortByName Should the list be automatically sorted by name
     *
     * @return array Formatted choices
     */
    public static function formatFormChoices(array $rawChoices, string $idKey, string $nameKey, bool $sortByName = true): array
    {
        // Remove items with duplicate name and ID
        $tmp = [];
        foreach ($rawChoices as $k => $rawChoice) {
            if (!empty($tmp[$rawChoice[$idKey]]) && $tmp[$rawChoice[$idKey]] == $rawChoice[$nameKey]) {
                unset($rawChoices[$k]);
                continue;
            }
            $tmp[$rawChoice[$idKey]] = $rawChoice[$nameKey];
        }
        unset($tmp);

        // Final array with choices
        $finalChoices = [];

        // A slim array with just they keys we processed, so we know what are duplicates
        // We can't use $finalChoices, because once we have the first two duplicates renamed, the third dupe won't match
        $alreadyProcessedKeys = [];

        foreach ($rawChoices as $k => $rawChoice) {
            // If we already came across this exact value name before, we will
            // append the option ID before the name.
            if (in_array($rawChoice[$nameKey], $alreadyProcessedKeys)) {
                // We store it with ID prepended before the name.
                $finalChoices[sprintf('%s (%d)', $rawChoice[$nameKey], $rawChoice[$idKey])] = $rawChoice[$idKey];

                // And if it's the first duplicate (second occurence), we also modify the previous normal one.
                if (isset($finalChoices[$rawChoice[$nameKey]])) {
                    $previousId = $finalChoices[$rawChoice[$nameKey]];
                    $finalChoices = self::replaceArrayKey(
                        $finalChoices,
                        $rawChoice[$nameKey],
                        sprintf('%s (%d)', $rawChoice[$nameKey], $previousId)
                    );
                }
            } else {
                // We store it in the final array normally
                $finalChoices[$rawChoice[$nameKey]] = $rawChoice[$idKey];
            }

            // For next time, we mark that we processed this option
            $alreadyProcessedKeys[] = $rawChoice[$nameKey];

            // And save some memory, we don't need it anymore
            unset($rawChoices[$k]);
        }

        // Order data by displayed value, if desired
        if ($sortByName) {
            ksort($finalChoices);
        }

        return $finalChoices;
    }

    /*
     * Renames a given array key without modifying it's position in the array.
     *
     * @param array $array Array to work on
     * @param string $oldKey Old array key
     * @param string $newKey New array key
     *
     * @return array Array with changed key
     */
    public static function replaceArrayKey($array, $oldKey, $newKey): array
    {
        $keys = array_keys($array);
        $keys[array_search($oldKey, $keys)] = $newKey;

        return array_combine($keys, $array);
    }
}
