<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address;

use CustomerAddress;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShopDatabaseException;

abstract class AbstractCustomerAddressHandler extends AbstractAddressHandler
{
    /**
     * @return string[]
     *
     * @throws AddressException
     */
    protected function getRequiredFields(): array
    {
        try {
            $requiredFields = (new CustomerAddress())->getFieldsRequiredDatabase();
        } catch (PrestaShopDatabaseException $e) {
            throw new AddressException('Something went wrong while retrieving required fields for address', 0, $e);
        }

        if (empty($requiredFields)) {
            return [];
        }

        $fields = [];

        foreach ($requiredFields as $field) {
            $fields[] = $field['field_name'];
        }

        return $fields;
    }
}
