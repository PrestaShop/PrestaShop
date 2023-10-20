<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        } catch (PrestaShopException $e) {
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
