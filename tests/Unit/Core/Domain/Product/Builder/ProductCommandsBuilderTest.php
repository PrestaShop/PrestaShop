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

namespace Tests\Unit\Core\Domain\Product\Builder;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\Builder\ProductCommandBuilderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\Builder\ProductCommandCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\Builder\ProductCommandsBuilder;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

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

        $nullBuilder = new AlwaysNullBuilder();
        yield [
            [],
            [$nullBuilder],
            $collection,
        ];

        $commandA = new FakeProductCommand($this->getProductId(), 'A');
        $commandB = new FakeProductCommand($this->getProductId(), 'B');

        $builderA = new ConditionBuilder(['field_a' => 'a'], $commandA);
        $builderB = new ConditionBuilder(['field_b' => 'b'], $commandB);

        yield [
            ['field_a' => 'c', 'field_b' => 'b'],
            [$nullBuilder, $builderA, $builderB],
            new ProductCommandCollection([$commandB]),
        ];

        yield [
            ['field_a' => 'a'],
            [$nullBuilder, $builderA, $builderB],
            new ProductCommandCollection([$commandA]),
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderA, $nullBuilder, $builderB],
            new ProductCommandCollection([$commandA, $commandB]),
        ];

        yield [
            ['field_a' => 'a', 'field_b' => 'b'],
            [$builderB, $builderA, $nullBuilder],
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
    public function buildCommand(ProductId $productId, array $formData)
    {
        foreach ($this->formCondition as $key => $value) {
            if (!isset($formData[$key]) || $formData[$key] !== $value) {
                return null;
            }
        }

        return $this->command;
    }
}

class AlwaysNullBuilder implements ProductCommandBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommand(ProductId $productId, array $formData)
    {
        return null;
    }
}
