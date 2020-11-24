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

namespace Tests\Unit\Core\Localization\CLDR;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\TerritoryData;

class LocaleTest extends TestCase
{
    /**
     * @param LocaleData $localeData
     * @param array|null $expected
     *
     * @dataProvider getLocaleDataTerritories
     */
    public function testTerritories(LocaleData $localeData, ?array $expected)
    {
        $locale = new Locale($localeData);
        $this->assertIsArray($locale->getAllTerritories());
        $this->assertEquals($expected, $locale->getAllTerritories());
    }

    public function getLocaleDataTerritories(): Generator
    {
        yield [
            new LocaleData(),
            [],
        ];

        $territoriesData = [
            (new TerritoryData())->setIsoCode('DK')->setName('Denmark'),
            (new TerritoryData())->setIsoCode('EC')->setName('Ecuador'),
            (new TerritoryData())->setIsoCode('FR')->setName('France'),
        ];
        yield [
            (new LocaleData())->setTerritories($territoriesData),
            $territoriesData,
        ];
    }
}
