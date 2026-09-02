<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilderInterface;

class ProductCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $commandBuilders
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $commandBuilders, array $expectedCommands)
    {
        $builder = new ProductCommandsBuilder($commandBuilders);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        $collection = [];
        yield [
            [],
            [],
            $collection,
        ];

        $alwaysEmptyBuilder = new AlwaysEmptyBuilder();
        yield [
            [],
            [$alwaysEmptyBuilder],
            $collection,
        ];

        $commandA = new FakeProductCommand($this->getProductId(), 'A');
        $commandB = new FakeProductCommand($this->getProductId(), 'B');

        $builderA = new ConditionBuilder(['field_a' => 'a'], $commandA);
        $builderB = new ConditionBuilder(['field_b' => 'b'], $commandB);

        yield [
            ['field_a' => 'c', 'field_b' => 'b'],
            [$alwaysEmptyBuilder, $builderA, $builderB],
            [$commandB],
        ];

        yield [
            ['field_a' => 'a'],
            [$alwaysEmptyBuilder, $builderA, $builderB],
            [$commandA],
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderA, $alwaysEmptyBuilder, $builderB],
            [$commandA, $commandB],
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderB, $builderA, $alwaysEmptyBuilder],
            [$commandB, $commandA],
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderA, $builderB]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            [$commandA, $commandB],
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderB, $builderA]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            [$commandB, $commandA],
        ];
    }
}

class FakeProductCommand
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var ProductId
     */
    public $productId;

    /**
     * @param ProductId $productId
     * @param mixed $value
     */
    public function __construct(ProductId $productId, $value)
    {
        $this->productId = $productId;
        $this->value = $value;
    }
}

class ConditionBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @var array
     */
    private $formCondition;

    /**
     * @var mixed
     */
    private $command;

    /**
     * @param array $formCondition
     * @param mixed $command
     */
    public function __construct(array $formCondition, $command)
    {
        $this->formCondition = $formCondition;
        $this->command = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        foreach ($this->formCondition as $key => $value) {
            if (!isset($formData[$key]) || $formData[$key] !== $value) {
                return [];
            }
        }

        return [$this->command];
    }
}

class AlwaysEmptyBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $shopConstraint): array
    {
        return [];
    }
}

class MultiCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * @var ProductCommandsBuilderInterface[]
     */
    private $builders;

    /**
     * MultiCommandsBuilder constructor.
     *
     * @param array $commandBuilders
     */
    public function __construct(array $commandBuilders)
    {
        $this->builders = $commandBuilders;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commands = [];
        foreach ($this->builders as $builder) {
            $commands = array_merge($commands, $builder->buildCommands($productId, $formData, $singleShopConstraint));
        }

        return $commands;
    }
}
