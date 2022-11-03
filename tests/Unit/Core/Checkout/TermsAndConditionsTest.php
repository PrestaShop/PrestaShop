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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
