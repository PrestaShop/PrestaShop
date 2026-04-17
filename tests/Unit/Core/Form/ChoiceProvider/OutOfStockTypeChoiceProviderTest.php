<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OutOfStockTypeChoiceProvider;

class OutOfStockTypeChoiceProviderTest extends ChoiceProviderTestCase
{
    /**
     * @dataProvider getExpectedChoices
     *
     * @param bool $outOfStockAvailable
     * @param array $expectedChoices
     */
    public function testItProvidesChoicesAsExpected(bool $outOfStockAvailable, array $expectedChoices): void
    {
        $mock = $this->createMock(ShopConfigurationInterface::class);
        $mock->expects($this->once())
            ->method('get')
            ->willReturn($outOfStockAvailable);

        $choiceProvider = new OutOfStockTypeChoiceProvider(
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
            false,
            [
                'Deny orders' => 0,
                'Allow orders' => 1,
                'Use default behavior (Deny orders)' => 2,
            ],
        ];

        yield [
            true,
            [
                'Deny orders' => 0,
                'Allow orders' => 1,
                'Use default behavior (Allow orders)' => 2,
            ],
        ];
    }
}
