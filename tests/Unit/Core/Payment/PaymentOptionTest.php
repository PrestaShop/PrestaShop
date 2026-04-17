<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
