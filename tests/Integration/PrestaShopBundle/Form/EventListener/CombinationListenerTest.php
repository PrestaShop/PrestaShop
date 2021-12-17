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

namespace Integration\PrestaShopBundle\Form\EventListener;

use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\CombinationListener;
use Symfony\Component\Form\FormEvents;
use Tests\Integration\PrestaShopBundle\Form\EventListener\FormListenerTestCase;
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
