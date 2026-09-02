<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Payment;

use DOMDocument;
use Exception;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;

class PaymentOptionFormDecoratorTest extends TestCase
{
    public function testAddHiddenSubmitButtonInsertsSubmitButtonIntoForm()
    {
        $decorator = new PaymentOptionFormDecorator();

        $form =
"<div>
<p>Yolo</p>
<form>
    <input type='text' name='card_number'>
</form>
</div>";

        $exp =
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

    public function testAddHiddenSubmitButtonReturnsFalseWhenMultipleForms()
    {
        $decorator = new PaymentOptionFormDecorator();
        $this->assertFalse(
            $decorator->addHiddenSubmitButton(
                '<form></form><form></form>',
                'OPTION_ID'
            )
        );
    }

    private function normalizeHTML($html)
    {
        $doc = new DOMDocument();
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
