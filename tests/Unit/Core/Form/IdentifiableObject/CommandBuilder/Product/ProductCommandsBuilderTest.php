<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandCollection;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilder;

class ProductCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $commandBuilders
     * @param ProductCommandCollection $expectedCommands
     */
    public function testBuildCommands(array $formData, array $commandBuilders, ProductCommandCollection $expectedCommands)
    {
        $builder = new ProductCommandsBuilder($commandBuilders);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        $collection = new ProductCommandCollection();
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
            new ProductCommandCollection([$commandB]),
        ];

        yield [
            ['field_a' => 'a'],
            [$alwaysEmptyBuilder, $builderA, $builderB],
            new ProductCommandCollection([$commandA]),
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderA, $alwaysEmptyBuilder, $builderB],
            new ProductCommandCollection([$commandA, $commandB]),
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderB, $builderA, $alwaysEmptyBuilder],
            new ProductCommandCollection([$commandB, $commandA]),
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderA, $builderB]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            new ProductCommandCollection([$commandA, $commandB]),
        ];

        $multiBuilder = new MultiCommandsBuilder([$builderB, $builderA]);
        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$multiBuilder, $alwaysEmptyBuilder],
            new ProductCommandCollection([$commandB, $commandA]),
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

class ConditionBuilder implements ProductCommandBuilderInterface
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
    public function buildCommand(ProductId $productId, array $formData): array
    {
        foreach ($this->formCondition as $key => $value) {
            if (!isset($formData[$key]) || $formData[$key] !== $value) {
                return [];
            }
        }

        return [$this->command];
    }
}

class AlwaysEmptyBuilder implements ProductCommandBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommand(ProductId $productId, array $formData): array
    {
        return [];
    }
}

class MultiCommandsBuilder implements ProductCommandBuilderInterface
{
    /**
     * @var ProductCommandBuilderInterface[]
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
    public function buildCommand(ProductId $productId, array $formData): array
    {
        $commands = [];
        foreach ($this->builders as $builder) {
            $commands = array_merge($commands, $builder->buildCommand($productId, $formData));
        }

        return $commands;
    }
}
