<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;

/**
 * Interface for service that handles order return  state editing command
 */
interface EditOrderReturnStateHandlerInterface
{
    public function handle(EditOrderReturnStateCommand $command);
}
