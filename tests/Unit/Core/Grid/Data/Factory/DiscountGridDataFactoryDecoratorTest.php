<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Data\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\DiscountGridDataFactoryDecorator;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountGridDataFactoryDecoratorTest extends TestCase
{
    /**
     * The query labels the badge from cart_rule_type_lang, which holds the default language's text
     * for every language, so a core type must be labelled from the catalogue instead.
     */
    public function testItTranslatesTheLabelOfACoreType(): void
    {
        $records = $this->getData([
            ['discount_type' => DiscountType::FREE_SHIPPING, 'discount_type_label' => 'On free shipping'],
            ['discount_type' => DiscountType::CART_LEVEL, 'discount_type_label' => 'On cart amount'],
        ]);

        $this->assertSame('translated:On free shipping', $records[0]['discount_type_label']);
        $this->assertSame('translated:On cart amount', $records[1]['discount_type_label']);
    }

    /**
     * A type provided by a module has no catalogue entry, so its stored name is all there is.
     */
    public function testItKeepsTheStoredLabelOfAModuleType(): void
    {
        $records = $this->getData([
            ['discount_type' => 'some_module_type', 'discount_type_label' => 'Remise du module'],
        ]);

        $this->assertSame('Remise du module', $records[0]['discount_type_label']);
    }

    /**
     * The expiration fallback this decorator already provided must keep working alongside it.
     */
    public function testItStillLabelsAnEmptyExpirationDate(): void
    {
        $records = $this->getData([
            ['discount_type' => DiscountType::CART_LEVEL, 'discount_type_label' => 'On cart amount', 'date_to' => null],
            ['discount_type' => DiscountType::CART_LEVEL, 'discount_type_label' => 'On cart amount', 'date_to' => '2026-01-01 00:00:00'],
        ]);

        $this->assertSame('translated:No end date', $records[0]['date_to']);
        $this->assertSame('2026-01-01 00:00:00', $records[1]['date_to']);
    }

    private function getData(array $records): array
    {
        $decorated = $this->createMock(GridDataFactoryInterface::class);
        $decorated->method('getData')->willReturn(new GridData(new RecordCollection($records), count($records), 'query'));

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id): string => 'translated:' . $id
        );

        $decorator = new DiscountGridDataFactoryDecorator($decorated, $translator);

        return $decorator->getData($this->createMock(SearchCriteriaInterface::class))->getRecords()->all();
    }
}
