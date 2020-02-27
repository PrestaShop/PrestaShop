<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
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
