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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\TerritoryData;

class TerritoryDataTest extends TestCase
{
    /**
     * @var TerritoryData
     */
    private $territoryData;

    /**
     * Setup tested dependency
     */
    public function setUp()
    {
        $this->territoryData = new TerritoryData();
    }

    /**
     * @param string $initial
     * @param string $override
     * @param string $expected
     *
     * @dataProvider providerIsoCode
     */
    public function testOverrideIsoCode(
        string $initial,
        string $override,
        string $expected
    ) {
        // Initial
        $this->territoryData->setName('FakeData');
        $this->territoryData->setIsoCode($initial);

        // Override
        $territoryData = new TerritoryData();
        $territoryData->setName('FakeData');
        $territoryData->setIsoCode($override);

        // Result
        $result = $this->territoryData->overrideWith($territoryData);
        $this->assertInstanceOf(TerritoryData::class, $result);
        $this->assertEquals($expected, $result->getIsoCode());
    }

    public function providerIsoCode(): array
    {
        return [
            [
                'FR',
                'FR',
                'FR',
            ],
            [
                'FR',
                'DK',
                'DK',
            ],
        ];
    }

    /**
     * @param string|null $initial
     * @param string|null $override
     * @param string $expected
     *
     * @dataProvider providerName
     */
    public function testOverrideName(
        ?string $initial,
        ?string $override,
        string $expected
    ) {
        $this->territoryData->setIsoCode('FD');
        $this->territoryData->setName($initial);

        // Override
        $territoryData = new TerritoryData();
        $territoryData->setIsoCode('FD');
        $territoryData->setName($override);

        // Result
        $result = $this->territoryData->overrideWith($territoryData);
        $this->assertInstanceOf(TerritoryData::class, $result);
        $this->assertEquals($expected, $result->getName());
    }

    public function providerName(): array
    {
        return [
            [
                'France',
                'France',
                'France',
            ],
            [
                'France',
                'Denmark',
                'Denmark',
            ],
        ];
    }
}
