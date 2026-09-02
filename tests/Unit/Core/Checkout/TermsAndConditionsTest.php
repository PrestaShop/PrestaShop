<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Checkout;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;

class TermsAndConditionsTest extends TestCase
{
    public function testSetTextInsertsLinks()
    {
        $terms = new TermsAndConditions();

        $this->assertEquals(
            'hello <a href="http://www.world.com" id="cta-ho-0">world</a>',
            $terms->setIdentifier('ho')
                ->setText('hello [world]', 'http://www.world.com')
                ->format()
        );
    }

    public function testSetTextInsertsSeveralLinks()
    {
        $terms = new TermsAndConditions();

        $this->assertEquals(
            'hello <a href="http://www.world.com" id="cta-hey-0">world</a> <a href="http://yay.com" id="cta-hey-1">yay</a>',
            $terms->setIdentifier('hey')
                ->setText('hello [world] [yay]', 'http://www.world.com', 'http://yay.com')
                ->format()
        );
    }

    public function testSetTextJustDoesntAddLinksWhenMissing()
    {
        $terms = new TermsAndConditions();

        $this->assertEquals(
            'hello world',
            $terms->setText('hello [world]')
                ->format()
        );
    }
}
