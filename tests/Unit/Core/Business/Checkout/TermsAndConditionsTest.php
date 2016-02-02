<?php
namespace PrestaShop\PrestaShop\Tests\Core\Checkout;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;

class TermsAndConditionsTest extends UnitTestCase
{
    private $terms;

    public function setup()
    {
        $this->terms = new TermsAndConditions;
    }

    public function test_SetText_InsertsLinks()
    {
        $this->assertEquals(
            'hello <a href="http://www.world.com">world</a>',
            $this->terms->setText('hello [world]', "http://www.world.com")->format()
        );
    }

    public function test_SetText_InsertsSeveralLinks()
    {
        $this->assertEquals(
            'hello <a href="http://www.world.com">world</a> <a href="http://yay.com">yay</a>',
            $this->terms->setText('hello [world] [yay]', "http://www.world.com", "http://yay.com")->format()
        );
    }

    public function test_SetText_JustDoesntAddLinksWhenMissing()
    {
        $this->assertEquals(
            'hello world',
            $this->terms->setText('hello [world]')->format()
        );
    }
}
