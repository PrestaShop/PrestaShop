<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerB2b\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\Command\AddCustomerB2bCommand;
use PrestaShop\PrestaShop\Core\Domain\CustomerB2b\ValueObject\CustomerB2bId;

interface AddCustomerB2bHandlerInterface
{
    public function handle(AddCustomerB2bCommand $command): CustomerB2bId;
}
