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

namespace Tests\Unit\Core\Payment;

use Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use DOMDocument;
use Exception;

class PaymentOptionFormDecoratorTest extends UnitTestCase
{
    public function test_addHiddenSubmitButton_inserts_submit_button_into_form()
    {
        $decorator = new PaymentOptionFormDecorator;

        $form =
"<div>
<p>Yolo</p>
<form>
    <input type='text' name='card_number'>
</form>
</div>";

        $exp  =
"<div>
<p>Yolo</p>
<form>
    <input type='text' name='card_number'>
<button style='display:none' id='pay-with-OPTION_ID' type='submit'></button>
</form>
</div>";

        $act = $decorator->addHiddenSubmitButton($form, 'OPTION_ID');
        $this->assertStringStartsWith('<div>', $act);
        $this->assertSameHTML($exp, $act);
    }

    public function test_addHiddenSubmitButton_returns_false_when_multiple_forms()
    {
        $decorator = new PaymentOptionFormDecorator;
        $this->assertFalse(
            $decorator->addHiddenSubmitButton(
                '<form></form><form></form>',
                'OPTION_ID'
            )
        );
    }

    private function normalizeHTML($html)
    {
        $doc = new DOMDocument;
        if (!$doc->loadHTML($html)) {
            throw new Exception('Invalid HTML.');
        }
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        return $doc->saveHTML();
    }

    private function assertSameHTML($exp, $act)
    {
        $this->assertEquals(
            $this->normalizeHTML($exp),
            $this->normalizeHTML($act)
        );
        return $this;
    }
}
