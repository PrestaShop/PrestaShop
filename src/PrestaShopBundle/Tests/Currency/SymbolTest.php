<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\Symbol;

class SymbolTest extends TestCase
{
    const STUB_SYMBOL_DEFAULT = 'PS$';
    const STUB_SYMBOL_NARROW  = '$';

    /**
     * @var Symbol
     */
    protected $symbol;

    public function setUp()
    {
        $this->symbol = new Symbol(self::STUB_SYMBOL_DEFAULT, self::STUB_SYMBOL_NARROW);
    }

    /**
     * Given a valid currency symbol object
     * When casting it as a string
     * This symbol's narrow notation should be retrieved
     */
    public function testToString()
    {
        $this->assertSame(self::STUB_SYMBOL_NARROW, (string)$this->symbol);
    }
}
