<?php
/**
 * 2007-2017 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
            'hello <a href="http://www.world.com" id="cta-ho-0">world</a>',
            $this->terms->setIdentifier('ho')->setText('hello [world]', "http://www.world.com")->format()
        );
    }

    public function test_SetText_InsertsSeveralLinks()
    {
        $this->assertEquals(
            'hello <a href="http://www.world.com" id="cta-hey-0">world</a> <a href="http://yay.com" id="cta-hey-1">yay</a>',
            $this->terms->setIdentifier('hey')->setText('hello [world] [yay]', "http://www.world.com", "http://yay.com")->format()
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
