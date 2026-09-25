<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use DateTime;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\ValueObject\CatalogPriceRuleId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CatalogPriceRuleFormDataProvider;

/**
 * A catalog price rule stores -1 in specific_price_rule.price to say it sets no price of its
 * own; Product::getPriceStatic() reads that as "keep the product price" with `price < 0`.
 * A price of 0 is a real price and makes the products free, so the form must tell them apart.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/33609
 */
class CatalogPriceRuleFormDataProviderTest extends TestCase
{
    public function testItTreatsANegativePriceAsNoPriceOfItsOwn(): void
    {
        $data = $this->dataForPrice('-1');

        self::assertTrue($data['leave_initial_price'], 'the checkbox has to be ticked for the sentinel');
        self::assertNull($data['price'], 'and the price field has to stay empty');
    }

    public function testItKeepsAPriceOfZeroVisible(): void
    {
        $data = $this->dataForPrice('0');

        self::assertFalse(
            $data['leave_initial_price'],
            'zero is a real price - the rule makes the products free - so the checkbox must stay clear'
        );
        self::assertSame('0', $data['price'], 'and the merchant has to be able to see it in the field');
    }

    public function testItKeepsAnOrdinaryPrice(): void
    {
        $data = $this->dataForPrice('20.5');

        self::assertFalse($data['leave_initial_price']);
        self::assertSame('20.5', $data['price']);
    }

    /**
     * @return array<string, mixed>
     */
    private function dataForPrice(string $price): array
    {
        $rule = new EditableCatalogPriceRule(
            new CatalogPriceRuleId(1),
            'A rule',
            1,
            1,
            0,
            0,
            1,
            new DecimalNumber($price),
            new Reduction(Reduction::TYPE_AMOUNT, '0'),
            true,
            new DateTime('2023-01-01 00:00:00'),
            new DateTime('2030-01-01 00:00:00')
        );

        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturn($rule);

        return (new CatalogPriceRuleFormDataProvider($queryBus))->getData(1);
    }
}
