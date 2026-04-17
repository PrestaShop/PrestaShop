<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\EditOrderStateCommand;

/**
 * Interface for service that handles order state editing command
 */
interface EditOrderStateHandlerInterface
{
    public function handle(EditOrderStateCommand $command);
}
