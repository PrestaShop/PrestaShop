<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCommandsBuilderInterface;

class CombinationCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
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
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
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
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
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
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
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
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $commands = [];
        foreach ($this->builders as $builder) {
            $commands = array_merge($commands, $builder->buildCommands($combinationId, $formData, $singleShopConstraint));
        }

        return $commands;
    }
}
