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

namespace Tests\PrestaShopBundle\Localization\Specification;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Localization\Specification\Number as NumberSpecification;
use PrestaShopBundle\Localization\Specification\NumberSymbolList;

class NumberTest extends TestCase
{
    /**
     * @var NumberSpecification
     */
    protected $latinNumberSpec;

    /**
     * @var NumberSymbolList
     */
    protected $latinSymbolList;

    /**
     * @var NumberSymbolList
     */
    protected $arabSymbolList;

    protected function setUp()
    {
        $this->latinSymbolList = $this->getMockBuilder(NumberSymbolList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->arabSymbolList  = $this->getMockBuilder(NumberSymbolList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->latinNumberSpec = new NumberSpecification(
            '',
            '',
            ['latin' => $this->latinSymbolList, 'arab' => $this->arabSymbolList],
            3,
            0,
            true,
            3,
            3
        );
    }

    /**
     * Given a valid Number specification
     * When adding several symbols lists
     * Then calling getAllSymbols() should return an array of available symbols lists, indexed by numbering system
     *
     * (also tests addSymbols() at the same time)
     */
    public function testGetAllSymbolsReturnsAListOfSymbols()
    {
        $this->assertSame(
            [
                'latin' => $this->latinSymbolList,
                'arab'  => $this->arabSymbolList,
            ],
            $this->latinNumberSpec->getAllSymbols()
        );
    }

    /**
     * Given a valid Number specification
     * When asking it a symbols list for a given numbering system
     * Then the good list should be retrieved
     */
    public function testGetSymbolsByNumberingSystem()
    {
        $this->assertSame(
            $this->latinSymbolList,
            $this->latinNumberSpec->getSymbolsByNumberingSystem('latin')
        );
    }

    /**
     * Given a valid Number specification
     * When asking it a symbols list for a given INVALID numbering system
     * Then an exception souhd be raised
     *
     * @expectedException \PrestaShopBundle\Localization\Exception\LocalizationException
     */
    public function testGetSymbolsByNumberingSystemWithInvalidParameter()
    {
        $this->latinNumberSpec->getSymbolsByNumberingSystem('foobar');
    }
}
