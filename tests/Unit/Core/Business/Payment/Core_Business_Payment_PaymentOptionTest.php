<?php
/**
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Payment;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use Core_Business_Payment_PaymentOption as PaymentOption;

class Core_Business_Payment_PaymentOptionTest extends UnitTestCase
{
    public function test_convertLegacyOption_converts_one_option()
    {
        $newOption = new PaymentOption;
        $newOption
            ->setCallToActionText('Pay by bankwire')
            ->setLogo('http://example.com/logo.png')
            ->setAction('http://example.com/submit')
            ->setMethod('POST')
            ->setForm(null)
            ->setInputs(array('key' => 42))
        ;

        $legacyOption = array(
            'cta_text'  => 'Pay by bankwire',
            'logo'      => 'http://example.com/logo.png',
            'action'    => 'http://example.com/submit',
            'method'    => 'POST',
            'form'      => null,
            'inputs'    => array('key' => 42)
        );

        $this->assertEquals(
            array($newOption),
            PaymentOption::convertLegacyOption($legacyOption)
        );
    }

    public function test_convertLegacyOption_converts_two_options_specified_as_one()
    {
        $newOption = new PaymentOption;
        $newOption
            ->setCallToActionText('Pay by bankwire')
            ->setLogo('http://example.com/logo.png')
            ->setAction('http://example.com/submit')
            ->setMethod('POST')
            ->setForm(null)
            ->setInputs(array('key' => 42))
        ;

        $singleLegacyOption = array(
            'cta_text'  => 'Pay by bankwire',
            'logo'      => 'http://example.com/logo.png',
            'action'    => 'http://example.com/submit',
            'method'    => 'POST',
            'form'      => null,
            'inputs'    => array('key' => 42)
        );

        $legacyOption = array($singleLegacyOption, $singleLegacyOption);

        $this->assertEquals(
            array($newOption, $newOption),
            PaymentOption::convertLegacyOption($legacyOption)
        );
    }
}
