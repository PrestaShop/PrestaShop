<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Command\SetCustomerTradeCreditCommand;

interface SetCustomerTradeCreditHandlerInterface
{
    public function handle(SetCustomerTradeCreditCommand $command): void;
}
