<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier;

use Address;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShopException;
use Supplier;

/**
 * Provides reusable methods for supplier command/query handlers
 */
abstract class AbstractSupplierHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy Supplier
     *
     * @param SupplierId $supplierId
     *
     * @return Supplier
     *
     * @throws SupplierException
     */
    protected function getSupplier(SupplierId $supplierId)
    {
        try {
            $supplier = new Supplier($supplierId->getValue());
        } catch (PrestaShopException $e) {
            throw new SupplierException('Failed to create new supplier', 0, $e);
        }

        if ($supplier->id !== $supplierId->getValue()) {
            throw new SupplierNotFoundException(sprintf('Supplier with id "%s" was not found.', $supplierId->getValue()));
        }

        return $supplier;
    }

    /**
     * @param SupplierId $supplierId
     *
     * @return Address
     *
     * @throws SupplierException
     */
    protected function getSupplierAddress(SupplierId $supplierId)
    {
        $supplierIdValue = $supplierId->getValue();
        try {
            $addressId = Address::getAddressIdBySupplierId($supplierIdValue);

            $address = new Address($addressId);

            if (null === $address->id_supplier) {
                throw new AddressNotFoundException(sprintf('Address for supplier with id "%s" was not found', $supplierIdValue));
            }
        } catch (PrestaShopException $e) {
            throw new SupplierException('Failed to get supplier address', 0, $e);
        }

        return $address;
    }

    protected function removeSupplier(SupplierId $supplierId)
    {
        $supplier = $this->getSupplier($supplierId);

        try {
            return $supplier->delete();
        } catch (PrestaShopException) {
            throw new SupplierException(sprintf('An error occurred when deleting Supplier object with id "%s".', $supplier->id));
        }
    }

    /**
     * @param Supplier $supplier
     * @param Address $address
     *
     * @throws PrestaShopException
     * @throws SupplierException
     */
    protected function validateFields(Supplier $supplier, Address $address)
    {
        if (false === $supplier->validateFields(false) || false === $supplier->validateFieldsLang(false)) {
            throw new SupplierException('Supplier contains invalid field values');
        }

        if (false === $address->validateFields(false) || false === $address->validateFieldsLang(false)) {
            throw new SupplierException('Supplier address contains invalid field values');
        }
    }
}
