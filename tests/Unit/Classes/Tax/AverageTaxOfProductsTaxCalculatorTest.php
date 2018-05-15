<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Classes\Tax;

use Tests\TestCase\UnitTestCase;
use AverageTaxOfProductsTaxCalculator;
use Phake;

class AverageTaxOfProductsTaxCalculatorTest extends UnitTestCase
{
    public function test_tax_is_split_according_to_share_of_each_tax_rate()
    {
        $db = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface');
        $configuration = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface');

        $taxCalculator = new AverageTaxOfProductsTaxCalculator($db, $configuration);

        Phake::when($db)->select(Phake::anyParameters())->thenReturn(array(
            array('id_tax' => 1, 'rate' => 10, 'total_price_tax_excl' => 20),
            array('id_tax' => 2, 'rate' => 20, 'total_price_tax_excl' => 10)
        ));

        $amounts = $taxCalculator->getTaxesAmount(7, null, 2, PS_ROUND_HALF_UP);


        $expected = array(
            1 => round(7 * 20  / (20 + 10) * 0.1, 2),
            2 => round(7 * 10  / (20 + 10) * 0.2, 2)
        );

        $this->assertEquals($expected, $amounts);
    }
}
