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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Query\GetOrderReturnForEditing;
use PrestaShopDatabaseException;
use PrestaShopException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderReturnFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I change order return :orderReturnReference state to :orderReturnStateReference
     *
     * @param string $orderReturnReference
     * @param string $orderReturnStateReference
     *
     * @throws OrderReturnConstraintException
     */
    public function updateOrderReturnState(string $orderReturnReference, string $orderReturnStateReference): void
    {
        $orderReturnId = $this->getSharedStorage()->get($orderReturnReference);
        $orderReturnStateId = $this->getSharedStorage()->get($orderReturnStateReference);

        $this->getCommandBus()->handle(
            new UpdateOrderReturnStateCommand(
                (int) $orderReturnId,
                (int) $orderReturnStateId
            )
        );
    }

    /**
     * @Given :orderReturnReference has state :orderReturnStateReference
     *
     * @param string $orderReturnReference
     * @param string $orderReturnStateReference
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function checkOrderReturnState(string $orderReturnReference, string $orderReturnStateReference): void
    {
        $orderReturnId = SharedStorage::getStorage()->get($orderReturnReference);

        $orderReturn = $this->getCommandBus()->handle(new GetOrderReturnForEditing((int) $orderReturnId));
        $orderReturnStateId = SharedStorage::getStorage()->get($orderReturnStateReference);
        if ($orderReturn->getOrderReturnStateId() !== $orderReturnStateId) {
            $errorMessage = sprintf('Invalid order state for  %s, expected %s but got %s', $orderReturnReference, $orderReturnStateId, $orderReturn->getOrderReturnStateId());
            throw new RuntimeException($errorMessage);
        }
    }
}
