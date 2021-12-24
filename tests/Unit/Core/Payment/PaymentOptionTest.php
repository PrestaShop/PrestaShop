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

namespace Tests\Unit\Core\Payment;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PaymentOptionTest extends TestCase
{
    public function testConvertLegacyOptionConvertsOneOption()
    {
        $newOption = new PaymentOption();
        $newOption
            ->setCallToActionText('Pay by bankwire')
            ->setLogo('http://example.com/logo.png')
            ->setAction('http://example.com/submit')
            ->setForm(null)
            ->setInputs(['key' => 42]);

        $legacyOption = [
            'cta_text' => 'Pay by bankwire',
            'logo' => 'http://example.com/logo.png',
            'action' => 'http://example.com/submit',
            'form' => null,
            'inputs' => ['key' => 42],
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
            ->setInputs(['key' => 42]);

        $singleLegacyOption = [
            'cta_text' => 'Pay by bankwire',
            'logo' => 'http://example.com/logo.png',
            'action' => 'http://example.com/submit',
            'form' => null,
            'inputs' => ['key' => 42],
        ];

        $legacyOption = [$singleLegacyOption, $singleLegacyOption];

        $this->assertEquals(
            [$newOption, $newOption],
            PaymentOption::convertLegacyOption($legacyOption)
        );
    }
}
