<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\TypeCommandsBuilder;

class TypeCommandBuilderTest extends AbstractProductCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new TypeCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        yield [
            [
                'no_type_data' => ['useless value'],
            ],
            [],
        ];

        $command = new UpdateProductTypeCommand(
            $this->getProductId()->getValue(),
            ProductType::TYPE_STANDARD
        );
        yield [
            [
                'header' => [
                    'type' => ProductType::TYPE_STANDARD,
                ],
            ],
            [$command],
        ];
    }
}
