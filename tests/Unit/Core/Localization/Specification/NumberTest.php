<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\Specification;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberSymbolList;

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

    protected function setUp(): void
    {
        $this->latinSymbolList = $this->getMockBuilder(NumberSymbolList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->arabSymbolList = $this->getMockBuilder(NumberSymbolList::class)
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
                'arab' => $this->arabSymbolList,
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
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->assertSame(
            $this->latinSymbolList,
            $this->latinNumberSpec->getSymbolsByNumberingSystem('latin')
        );
        /* @noinspection end */
    }

    /**
     * Given a valid Number specification
     * When asking it a symbols list for a given INVALID numbering system
     * Then an exception souhd be raised
     */
    public function testGetSymbolsByNumberingSystemWithInvalidParameter()
    {
        $this->expectException(LocalizationException::class);

        $this->latinNumberSpec->getSymbolsByNumberingSystem('foobar');
    }
}
