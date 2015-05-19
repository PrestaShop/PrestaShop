<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Business\Payment;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;

use Core_Business_Payment_PaymentOption as PaymentOption;

class Core_Business_Payment_PaymentOptionTest extends UnitTestCase
{
    public function test_convertLegacyOption_converts_one_option()
    {
        $newOption = new PaymentOption;
        $newOption
            ->setCtaText('Pay by bankwire')
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
            ->setCtaText('Pay by bankwire')
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
