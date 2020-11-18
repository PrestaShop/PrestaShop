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

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;
use Language;

/**
 * Contains methods to transform string array into localized array
 */
class StringToLocalizedArrayTransformContext implements Context
{
    /**
     * @Transform /((\{[a-z]{2})-([A-Z]{2}:.*?\}))/
     *
     * @param string $string expected string e.g. {en-US:test;fr-FR:test2}
     *
     * @return array<int, string> [langId => value]
     */
    public function transformStringToLocalizedArray(string $string): array
    {
        $string = str_replace(['{', '}'], '', $string);
        $arrayValues = array_map('trim', explode(';', $string));
        $localizedArray = [];
        foreach ($arrayValues as $arrayValue) {
            $data = explode(':', $arrayValue);
            $langKey = $data[0];
            $langValue = $data[1];
            if (ctype_digit($langKey)) {
                $localizedArray[$langKey] = $langValue;
            } else {
                $localizedArray[(int) Language::getIdByLocale($langKey, true)] = $langValue;
            }
        }

        return $localizedArray;
    }
}
