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

namespace LegacyTests\Unit\Core\Payment;

use LegacyTests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption as PaymentOption;

class Core_Payment_PaymentOptionTest extends UnitTestCase
{
    public function testConvertLegacyOptionConvertsOneOption()
    {
        $newOption = new PaymentOption();
        $newOption
            ->setCallToActionText('Pay by bankwire')
            ->setLogo('http://example.com/logo.png')
            ->setAction('http://example.com/submit')
            ->setForm(null)
            ->setInputs(['key' => 42])
        ;

        $legacyOption = [
            'cta_text'  => 'Pay by bankwire',
            'logo'      => 'http://example.com/logo.png',
            'action'    => 'http://example.com/submit',
            'form'      => null,
            'inputs'    => ['key' => 42]
        ];

        $this->assertEquals(
            [$newOption],
            PaymentOption::convertLegacyOption($legacyOption)
        );
    }

    public function testConvertLegacyOptionConvertsTwoOptionsSpecifiedAsOne()
    {
        $newOption = new PaymentOption();
        $newOption
            ->setCallToActionText('Pay by bankwire')
            ->setLogo('http://example.com/logo.png')
            ->setAction('http://example.com/submit')
            ->setForm(null)
            ->setInputs(['key' => 42])
        ;

        $singleLegacyOption = [
            'cta_text'  => 'Pay by bankwire',
            'logo'      => 'http://example.com/logo.png',
            'action'    => 'http://example.com/submit',
            'form'      => null,
            'inputs'    => ['key' => 42]
        ];

        $legacyOption = [$singleLegacyOption, $singleLegacyOption];

        $this->assertEquals(
            [$newOption, $newOption],
            PaymentOption::convertLegacyOption($legacyOption)
        );
    }
}
