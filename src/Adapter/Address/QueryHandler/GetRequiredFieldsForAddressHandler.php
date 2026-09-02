<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Address\AbstractCustomerAddressHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler\GetRequiredFieldsForAddressHandlerInterface;

/**
 * Handles query which gets required fields for address
 *
 * @internal
 */
#[AsQueryHandler]
final class GetRequiredFieldsForAddressHandler extends AbstractCustomerAddressHandler implements GetRequiredFieldsForAddressHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressException
     */
    public function handle(GetRequiredFieldsForAddress $query): array
    {
        return $this->getRequiredFields();
    }
}
