<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\BasicInformationFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

class BasicInformationFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $this->mockDefaultProduct(),
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
        $command = $this->getEmptyCommand();

        yield [$command, [], $this->mockDefaultProduct()];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([
                self::DEFAULT_LANG_ID => 'My name',
                2 => 'Your name',
            ])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->name = [
            self::DEFAULT_LANG_ID => 'My name',
            2 => 'Your name',
        ];

        yield [
            $command,
            ['name' => [self::DEFAULT_LANG_ID, 2]],
            $expectedProduct,
        ];

        $command = $this
            ->getEmptyCommand()
            ->setLocalizedNames([self::DEFAULT_LANG_ID => 'My name'])
            ->setLocalizedShortDescriptions([
                self::DEFAULT_LANG_ID => 'short desc 1',
                2 => 'short desc 2',
            ])
            ->setLocalizedDescriptions([self::DEFAULT_LANG_ID => 'desc 1'])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->name = [self::DEFAULT_LANG_ID => 'My name'];
        $expectedProduct->description_short = [
            self::DEFAULT_LANG_ID => 'short desc 1',
            2 => 'short desc 2',
        ];
        $expectedProduct->description = [self::DEFAULT_LANG_ID => 'desc 1'];

        yield [
            $command,
            [
                'name' => [self::DEFAULT_LANG_ID],
                'description' => [self::DEFAULT_LANG_ID],
                'description_short' => [self::DEFAULT_LANG_ID, 2],
            ],
            $expectedProduct,
        ];
    }

    /**
     * @return BasicInformationFiller
     */
    private function getFiller(): BasicInformationFiller
    {
        return new BasicInformationFiller(
            self::DEFAULT_LANG_ID
        );
    }
}
