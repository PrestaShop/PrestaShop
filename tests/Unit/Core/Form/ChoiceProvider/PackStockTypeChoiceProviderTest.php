<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\PackStockTypeChoiceProvider;

class PackStockTypeChoiceProviderTest extends ChoiceProviderTestCase
{
    /**
     * @dataProvider getExpectedChoices
     *
     * @param int $defaultPackStockType
     * @param array $expectedChoices
     */
    public function testItProvidesChoicesAsExpected(int $defaultPackStockType, array $expectedChoices): void
    {
        $mock = $this->createMock(ShopConfigurationInterface::class);
        $mock->expects($this->once())
            ->method('get')
            ->willReturn($defaultPackStockType);

        $choiceProvider = new PackStockTypeChoiceProvider(
            $this->mockTranslator(),
            $mock
        );

        $this->assertEquals($expectedChoices, $choiceProvider->getChoices());
    }

    /**
     * @return Generator
     */
    public function getExpectedChoices(): Generator
    {
        yield [
            0,
            [
                'Use pack quantity' => 0,
                'Use quantity of products in the pack' => 1,
                'Use both, whatever is lower' => 2,
                'Default (Use pack quantity)' => 3,
            ],
        ];
        yield [
            1,
            [
                'Use pack quantity' => 0,
                'Use quantity of products in the pack' => 1,
                'Use both, whatever is lower' => 2,
                'Default (Use quantity of products in the pack)' => 3,
            ],
        ];
        yield [
            2,
            [
                'Use pack quantity' => 0,
                'Use quantity of products in the pack' => 1,
                'Use both, whatever is lower' => 2,
                'Default (Use both, whatever is lower)' => 3,
            ],
        ];
    }
}
