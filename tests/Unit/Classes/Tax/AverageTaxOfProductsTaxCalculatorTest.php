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

namespace Tests\Unit\Classes\Tax;

use AverageTaxOfProductsTaxCalculator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Foundation\Database\DatabaseInterface;

class AverageTaxOfProductsTaxCalculatorTest extends TestCase
{
    public function testTaxIsSplitAccordingToShareOfEachTaxRate(): void
    {
        $db = $this->createMock(DatabaseInterface::class);
        $db->method('select')->withAnyParameters()->willReturn([
            ['id_tax' => 1, 'rate' => 10, 'total_price_tax_excl' => 20],
            ['id_tax' => 2, 'rate' => 20, 'total_price_tax_excl' => 10],
        ]);
        $configuration = $this->createMock(ConfigurationInterface::class);

        $taxCalculator = new AverageTaxOfProductsTaxCalculator($db, $configuration);

        $amounts = $taxCalculator->getTaxesAmount(7, null, 2, PS_ROUND_HALF_UP);

        $expected = [
            1 => round(7 * 20 / (20 + 10) * 0.1, 2),
            2 => round(7 * 10 / (20 + 10) * 0.2, 2),
        ];

        $this->assertEquals($expected, $amounts);
    }
}
