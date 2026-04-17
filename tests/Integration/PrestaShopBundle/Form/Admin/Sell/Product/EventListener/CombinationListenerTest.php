<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\CombinationListener;
use Symfony\Component\Form\FormEvents;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;
use Tests\Integration\PrestaShopBundle\Form\TestCombinationFormType;

class CombinationListenerTest extends FormListenerTestCase
{
    public function testSubscribedEvents(): void
    {
        // Only events are relevant, the matching function is up to implementation
        $expectedSubscribedEvents = [
            FormEvents::PRE_SET_DATA,
            FormEvents::PRE_SUBMIT,
        ];
        $subscribedEvents = CombinationListener::getSubscribedEvents();
        $this->assertSame($expectedSubscribedEvents, array_keys($subscribedEvents));
    }

    /**
     * @dataProvider getStockMovements
     *
     * @param array $movementsData
     * @param bool $shouldExist
     */
    public function testStockMovementsRemovedBasedOnItsContent(array $movementsData, bool $shouldExist): void
    {
        $formData = [
            'stock' => [
                'quantities' => [
                    'stock_movements' => $movementsData,
                ],
            ],
        ];
        $form = $this->createForm(TestCombinationFormType::class, [], $formData);

        $this->assertFormTypeExistsInForm($form, 'stock.quantities.stock_movements', true);

        $eventMock = $this->createEventMock($formData, $form);
        $listener = new CombinationListener();
        $listener->adaptCombinationForm($eventMock);

        $this->assertFormTypeExistsInForm($form, 'stock.quantities.stock_movements', $shouldExist);
    }

    public function getStockMovements(): iterable
    {
        yield [[], false];

        $stockMovements = [
            [
                'employee' => 'John Doe',
                'delta_quantity' => 42,
            ],
            [
                'employee' => 'John Doe',
                'delta_quantity' => -15,
            ],
        ];

        yield [$stockMovements, true];
    }
}
