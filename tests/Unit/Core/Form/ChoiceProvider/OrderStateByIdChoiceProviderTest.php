<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use Generator;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OrderStateByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Order\OrderStateDataProviderInterface;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;

class OrderStateByIdChoiceProviderTest extends ChoiceProviderTestCase
{
    private const LANG_ID = 1;

    /**
     * @dataProvider getChoicesProvider
     */
    public function testGetChoices(array $options, array $expectedChoices): void
    {
        $orderStateDataProvider = $this->createMock(OrderStateDataProviderInterface::class);
        $orderStateDataProvider
            ->method('getOrderStates')
            ->with(self::LANG_ID)
            ->willReturn([
                ['id_order_state' => '1', 'name' => 'Awaiting payment', 'color' => '#34209E', 'deleted' => '0'],
                ['id_order_state' => '2', 'name' => 'Shipped', 'color' => '#01B887', 'deleted' => '0'],
                ['id_order_state' => '3', 'name' => 'Old status', 'color' => '#FFFFFF', 'deleted' => '1'],
                ['id_order_state' => '4', 'name' => 'Legacy status', 'color' => '#000000', 'deleted' => '1'],
            ]);

        $choiceProvider = new OrderStateByIdChoiceProvider(
            self::LANG_ID,
            $orderStateDataProvider,
            new ColorBrightnessCalculator(),
            $this->mockTranslator()
        );

        $this->assertEquals($expectedChoices, $choiceProvider->getChoices($options));
    }

    public static function getChoicesProvider(): Generator
    {
        $activeChoices = [
            'Awaiting payment' => '1',
            'Shipped' => '2',
        ];

        yield 'no options includes deleted states' => [
            [],
            $activeChoices + ['Old status (deleted)' => '3', 'Legacy status (deleted)' => '4'],
        ];

        yield 'include_deleted true includes deleted states' => [
            ['include_deleted' => true],
            $activeChoices + ['Old status (deleted)' => '3', 'Legacy status (deleted)' => '4'],
        ];

        yield 'include_deleted false excludes deleted states' => [
            ['include_deleted' => false],
            $activeChoices,
        ];

        yield 'deleted current state is the only deleted state kept' => [
            ['current_state' => 3],
            $activeChoices + ['Old status (deleted)' => '3'],
        ];

        yield 'active current state excludes deleted states' => [
            ['current_state' => 1],
            $activeChoices,
        ];

        yield 'include_deleted false takes precedence over current_state' => [
            ['current_state' => 3, 'include_deleted' => false],
            $activeChoices,
        ];
    }
}
