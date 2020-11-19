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

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Language;
use RuntimeException;

/**
 * Parses localized array
 */
class LocalizedArrayParser
{
    /**
     * @var array<string, int>
     */
    private $langIdsByLocale;

    /**
     * @param array<string, int> $langIdsByLocale [$locale => $id] e.g. [fr-FR => 2]
     */
    public function __construct(
        array $langIdsByLocale = []
    ) {
        $this->langIdsByLocale = $langIdsByLocale;
    }

    /**
     * Parses localized string into localized array
     *
     * @param string $string e.g. 'en-US:foo;fr-FR:bar'
     *
     * @return array<int, string> [$langId => $value]
     */
    public function parseStringToArray(string $string): array
    {
        $arrayValues = array_map('trim', explode(';', $string));
        $localizedArray = [];
        foreach ($arrayValues as $arrayValue) {
            $data = explode(':', $arrayValue);
            if (!isset($data[0], $data[1])) {
                throw new RuntimeException('Invalid localized string provided. Expected e.g. \'en-US:foo;fr-FR:bar\'|\'en-US:foo');
            }
            $langKey = $data[0];
            $langValue = $data[1];
            if (ctype_digit($langKey)) {
                $localizedArray[$langKey] = $langValue;
            } else {
                $localizedArray[$this->getIdByLocale($langKey)] = $langValue;
            }
        }

        return $localizedArray;
    }

    /**
     * @param string $locale
     *
     * @return int
     */
    private function getIdByLocale(string $locale): int
    {
        if (empty($this->langIdsByLocale)) {
            $id = (int) Language::getIdByLocale($locale, true);
        } else {
            $id = isset($this->langIdsByLocale[$locale]) ? $this->langIdsByLocale[$locale] : 0;
        }

        if (!$id) {
            throw new RuntimeException(sprintf('lang id not found by locale %s', $locale));
        }

        return $id;
    }
}
