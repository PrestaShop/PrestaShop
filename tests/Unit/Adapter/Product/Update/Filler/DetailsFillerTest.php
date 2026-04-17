<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\DetailsFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

class DetailsFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $product,
            $command,
            $expectedUpdatableProperties,
            $expectedProduct
        );
    }

    /**
     * @return iterable
     */
    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $command = $this->getEmptyCommand()
            ->setUpc('3456789')
            ->setIsbn('978-3-16-148410-1')
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->upc = '3456789';
        $expectedProduct->isbn = '978-3-16-148410-1';

        yield [
            $this->mockDefaultProduct(),
            $command,
            ['isbn', 'upc'],
            $expectedProduct,
        ];

        $command = $this->getEmptyCommand()
            ->setGtin('1234567890111')
            ->setIsbn('978-3-16-148410-0')
            ->setMpn('HUE222-7')
            ->setReference('ref-HUE222-7')
            ->setUpc('0123456789')
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->ean13 = '1234567890111';
        $expectedProduct->isbn = '978-3-16-148410-0';
        $expectedProduct->mpn = 'HUE222-7';
        $expectedProduct->reference = 'ref-HUE222-7';
        $expectedProduct->upc = '0123456789';

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'ean13',
                'isbn',
                'mpn',
                'reference',
                'upc',
            ],
            $expectedProduct,
        ];
    }

    /**
     * @return DetailsFiller
     */
    private function getFiller(): DetailsFiller
    {
        return new DetailsFiller();
    }
}
