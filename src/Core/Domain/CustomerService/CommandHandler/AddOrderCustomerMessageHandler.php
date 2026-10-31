<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Adapter\CustomerMessage\CommandHandler\AddOrderCustomerMessageHandler as AdapterAddOrderCustomerMessageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;

/**
 * @deprecated since 9.3, will be removed in the next major version - use \PrestaShop\PrestaShop\Adapter\CustomerMessage\CommandHandler\AddOrderCustomerMessageHandler instead
 */
#[AsCommandHandler]
class AddOrderCustomerMessageHandler extends AdapterAddOrderCustomerMessageHandler
{
}
