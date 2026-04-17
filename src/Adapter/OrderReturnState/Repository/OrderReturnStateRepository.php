<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\Repository;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;

class OrderReturnStateRepository extends AbstractObjectModelRepository
{
    /**
     * Gets legacy OrderReturnState
     *
     * @param OrderReturnStateId $orderReturnStateId
     *
     * @return OrderReturnState
     *
     * @throws CoreException
     * @throws OrderReturnStateNotFoundException
     */
    public function get(OrderReturnStateId $orderReturnStateId): OrderReturnState
    {
        try {
            $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());

            if ((int) $orderReturnState->id !== $orderReturnStateId->getValue()) {
                throw new OrderReturnStateNotFoundException($orderReturnStateId, sprintf('%s #%d was not found', OrderReturnState::class, $orderReturnStateId->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf(
                    'Error occurred when trying to get %s #%d [%s]',
                    OrderReturnState::class,
                    $orderReturnStateId->getValue(),
                    $e->getMessage()
                ),
                0,
                $e
            );
        }

        return $orderReturnState;
    }
}
