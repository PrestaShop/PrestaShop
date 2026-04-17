<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
