<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\AddOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Interface for service that handles command that adds new order state
 */
interface AddOrderStateHandlerInterface
{
    /**
     * @return OrderStateId
     */
    public function handle(AddOrderStateCommand $command);
}
