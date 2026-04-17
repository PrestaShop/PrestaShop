<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationImagesCommandsBuilder;

class CombinationImagesCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationImagesCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        $command = new SetCombinationImagesCommand(
            $this->getCombinationId()->getValue(),
            [42, 51]
        );
        yield [
            [
                'images' => [
                    42,
                    51,
                ],
            ],
            [$command],
        ];

        $command = new RemoveAllCombinationImagesCommand($this->getCombinationId()->getValue());
        yield [
            [
                'images' => [],
            ],
            [$command],
        ];
    }
}
