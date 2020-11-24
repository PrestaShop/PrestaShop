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
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;

class LocaleDataTest extends TestCase
{
    /**
     * @var LocaleData
     */
    private $localeData;

    /**
     * Setup tested dependency
     */
    public function setUp()
    {
        $this->localeData = new LocaleData();
    }

    /**
     * @param array|null $territoriesInitial
     * @param array|null $territoriesOverride
     * @param array $territoriesExpected
     *
     * @dataProvider providerTerritories
     */
    public function testOverrideWithTerritories(
        ?array $territoriesInitial,
        ?array $territoriesOverride,
        array $territoriesExpected
    ) {
        // Initial
        if (null !== $territoriesInitial) {
            $this->localeData->setTerritories($territoriesInitial);
        }

        // Override
        $localeData = new LocaleData();
        if (null !== $territoriesOverride) {
            $localeData->setTerritories($territoriesOverride);
        }

        // Result
        $result = $this->localeData->overrideWith($localeData);
        $this->assertInstanceOf(LocaleData::class, $result);
        $this->assertEquals($territoriesExpected, $result->getTerritories());
    }

    public function providerTerritories(): Generator
    {
        yield [
            null,
            [],
            [],
        ];
        yield [
            null,
            ['FR'],
            ['FR'],
        ];
        yield [
            [],
            [],
            [],
        ];
        yield [
            [],
            ['FR'],
            ['FR'],
        ];
        yield [
            ['FR'],
            ['DK'],
            ['FR', 'DK'],
        ];
        yield [
            ['FR', 'EC'],
            ['DK'],
            ['FR', 'EC', 'DK'],
        ];
        yield [
            ['FR'],
            ['EC', 'DK'],
            ['FR', 'EC', 'DK'],
        ];
    }
}
