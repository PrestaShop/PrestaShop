<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Tax;

use PHPUnit_Framework_TestCase;
use Tax;
use TaxCalculator;

class TaxCalculatorCoreTest extends PHPUnit_Framework_TestCase
{
    public function test_getTotalRate_OK()
    {
        $tax = new Tax();
        $tax->rate = 20.6;
        $tax2 = new Tax();
        $tax2->rate = 5.5;

        $tax_calculator = new TaxCalculator(array(
            $tax, $tax2
        ), TaxCalculator::COMBINE_METHOD);

        $totalRate = $tax_calculator->getTotalRate();

        $this->assertEquals(26.1, $totalRate);
    }

    public function test_getTotalRate_Bug()
    {
        $tax = new Tax();
        $tax->rate = 20.6;
        $tax2 = new Tax();
        $tax2->rate = 5.5;

        $tax_calculator = new TaxCalculator(array(
            $tax, $tax2
        ), TaxCalculator::ONE_AFTER_ANOTHER_METHOD);

        $totalRate = $tax_calculator->getTotalRate();

        $this->assertEquals(27.233, $totalRate);
    }
}
