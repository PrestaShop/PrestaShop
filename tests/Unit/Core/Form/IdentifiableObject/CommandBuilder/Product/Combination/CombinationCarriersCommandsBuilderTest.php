<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationCarriersCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationCarriersCommandsBuilder;

class CombinationCarriersCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationCarriersCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): Generator
    {
        yield 'no carriers field' => [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield 'carriers selected' => [
            [
                'carriers' => [2, 5],
            ],
            [new SetCombinationCarriersCommand($this->getCombinationId()->getValue(), [2, 5], $this->getSingleShopConstraint())],
        ];

        yield 'no carrier selected' => [
            [
                'carriers' => [],
            ],
            [new SetCombinationCarriersCommand($this->getCombinationId()->getValue(), [], $this->getSingleShopConstraint())],
        ];
    }
}
