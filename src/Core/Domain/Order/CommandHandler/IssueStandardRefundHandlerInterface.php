<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueStandardRefundCommand;

/**
 * Interface for service that handles issuing standard refund for given order
 */
interface IssueStandardRefundHandlerInterface
{
    /**
     * @param IssueStandardRefundCommand $command
     */
    public function handle(IssueStandardRefundCommand $command): void;
}
