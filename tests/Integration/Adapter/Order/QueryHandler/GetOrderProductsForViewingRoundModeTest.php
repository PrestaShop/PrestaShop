<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\QueryHandler;

use Configuration;
use Context;
use Currency;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The back office offers six rounding modes; the decimal library implements five of them and has no
 * half-odd. Mapping the shop's mode to one of the library's threw for the sixth, so a shop set to
 * "Round towards the next odd value" could not open any order at all.
 */
class GetOrderProductsForViewingRoundModeTest extends KernelTestCase
{
    private const ORDER_ID = 1;

    /** @var int */
    private $originalRoundMode;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $context = Context::getContext();
        $context->container = self::getContainer();
        // Price computation reads the rounding precision off the context currency; without it
        // ComputingPrecision::getPrecision() is handed null.
        $context->currency = new Currency(Currency::getDefaultCurrencyId());

        $this->originalRoundMode = (int) Configuration::get('PS_PRICE_ROUND_MODE');
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_PRICE_ROUND_MODE', $this->originalRoundMode);
        parent::tearDown();
    }

    /**
     * @dataProvider provideEveryRoundModeOfferedInTheBackOffice
     */
    public function testAnOrderCanBeViewedWhateverRoundModeTheShopUses(int $roundMode, string $label): void
    {
        Configuration::updateValue('PS_PRICE_ROUND_MODE', $roundMode);

        $products = self::getContainer()
            ->get('prestashop.core.query_bus')
            ->handle(GetOrderProductsForViewing::all(self::ORDER_ID));

        $this->assertInstanceOf(
            OrderProductsForViewing::class,
            $products,
            sprintf('an order must be viewable with the round mode "%s"', $label)
        );
    }

    /**
     * The six choices of PreferencesType::price_round_mode, in the order the back office lists them.
     */
    public static function provideEveryRoundModeOfferedInTheBackOffice(): iterable
    {
        yield 'R1 round up away from zero' => [PS_ROUND_HALF_UP, 'Round up away from zero, when it is half way there'];
        yield 'R2 round down towards zero' => [PS_ROUND_HALF_DOWN, 'Round down towards zero, when it is half way there'];
        yield 'R3 round towards the next even value' => [PS_ROUND_HALF_EVEN, 'Round towards the next even value'];
        yield 'R4 round towards the next odd value' => [PS_ROUND_HALF_ODD, 'Round towards the next odd value'];
        yield 'R5 round up to the nearest value' => [PS_ROUND_UP, 'Round up to the nearest value'];
        yield 'R6 round down to the nearest value' => [PS_ROUND_DOWN, 'Round down to the nearest value'];
    }
}
