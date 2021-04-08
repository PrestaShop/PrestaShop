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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;

class CombinationCommandsBuilderTest extends AbstractCombinationCommandBuilderTest
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
        $builder = new CombinationCommandsBuilder($commandBuilders);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData);
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

        $commandA = new FakeCombinationCommand($this->getCombinationId(), 'A');
        $commandB = new FakeCombinationCommand($this->getCombinationId(), 'B');

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

class FakeCombinationCommand
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var CombinationId
     */
    public $combinationId;

    /**
     * @param CombinationId $combinationId
     * @param mixed $value
     */
    public function __construct(CombinationId $combinationId, $value)
    {
        $this->combinationId = $combinationId;
        $this->value = $value;
    }
}

class ConditionBuilder implements CombinationCommandsBuilderInterface
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
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        foreach ($this->formCondition as $key => $value) {
            if (!isset($formData[$key]) || $formData[$key] !== $value) {
                return [];
            }
        }

        return [$this->command];
    }
}

class AlwaysEmptyBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        return [];
    }
}

class MultiCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * @var CombinationCommandsBuilderInterface[]
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
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        $commands = [];
        foreach ($this->builders as $builder) {
            $commands = array_merge($commands, $builder->buildCommands($combinationId, $formData));
        }

        return $commands;
    }
}
