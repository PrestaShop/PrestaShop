<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;

/**
 * Interface for service that handles issuing partial refund for given order
 */
interface IssuePartialRefundHandlerInterface
{
    /**
     * @param IssuePartialRefundCommand $command
     */
    public function handle(IssuePartialRefundCommand $command): void;
}
