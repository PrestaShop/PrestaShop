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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Address;
use Db;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierAddressProvider;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierOrderValidator;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierAddressException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierProductRelationException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShopException;
use Supplier;

/**
 * Class AbstractDeleteSupplierHandler defines common actions required for
 * both BulkDeleteSupplierHandler and DeleteSupplierHandler.
 */
abstract class AbstractDeleteSupplierHandler
{
    /**
     * @var SupplierOrderValidator
     */
    private $supplierOrderValidator;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var SupplierAddressProvider
     */
    private $supplierAddressProvider;

    /**
     * @param SupplierOrderValidator $supplierOrderValidator
     * @param SupplierAddressProvider $supplierAddressProvider
     * @param string $dbPrefix
     */
    public function __construct(
        SupplierOrderValidator $supplierOrderValidator,
        SupplierAddressProvider $supplierAddressProvider,
        $dbPrefix
    ) {
        $this->supplierOrderValidator = $supplierOrderValidator;
        $this->dbPrefix = $dbPrefix;
        $this->supplierAddressProvider = $supplierAddressProvider;
    }

    /**
     * Removes supplier and all related content with it such as image, supplier and product relation
     * and supplier address.
     *
     * @param SupplierId $supplierId
     *
     * @throws SupplierException
     */
    protected function removeSupplier(SupplierId $supplierId)
    {
        try {
            $entity = new Supplier($supplierId->getValue());

            if (0 >= $entity->id) {
                throw new SupplierNotFoundException(sprintf('Supplier object with id "%s" was not found for deletion.', $supplierId->getValue()));
            }

            if ($this->hasPendingOrders($supplierId)) {
                throw new CannotDeleteSupplierException($supplierId->getValue(), sprintf('Supplier with id %s cannot be deleted due to it has pending orders', $supplierId->getValue()), CannotDeleteSupplierException::HAS_PENDING_ORDERS);
            }

            if (false === $this->deleteProductSupplierRelation($supplierId)) {
                throw new CannotDeleteSupplierProductRelationException(sprintf('Unable to delete suppliers with id "%s" product relation from product_supplier table', $supplierId->getValue()));
            }

            if (1 >= count($entity->getAssociatedShops()) && false === $this->deleteSupplierAddress($supplierId)) {
                throw new CannotDeleteSupplierAddressException(sprintf('Unable to set deleted flag for supplier with id "%s" address', $supplierId->getValue()));
            }

            if (false === $entity->delete()) {
                throw new SupplierException(sprintf('Unable to delete supplier object with id "%s"', $supplierId->getValue()));
            }
        } catch (PrestaShopException $exception) {
            throw new SupplierException(sprintf('An error occurred when deleting the supplier object with id "%s"', $supplierId->getValue()), 0, $exception);
        }
    }

    /**
     * Deletes product supplier relation.
     *
     * @param SupplierId $supplierId
     *
     * @return bool
     */
    private function deleteProductSupplierRelation(SupplierId $supplierId)
    {
        $sql = 'DELETE FROM `' . $this->dbPrefix . 'product_supplier` WHERE `id_supplier`=' . $supplierId->getValue();

        return Db::getInstance()->execute($sql);
    }

    /**
     * Deletes supplier address.
     *
     * @param SupplierId $supplierId
     *
     * @return bool
     */
    private function deleteSupplierAddress(SupplierId $supplierId)
    {
        $supplierAddressId = $this->supplierAddressProvider->getIdBySupplier($supplierId->getValue());

        $address = new Address($supplierAddressId);

        if ($address->id) {
            $address->deleted = true;

            return $address->update();
        }

        return true;
    }

    /**
     * Checks if the given supplier has pending orders.
     *
     * @param SupplierId $supplierId
     *
     * @return bool
     */
    private function hasPendingOrders(SupplierId $supplierId)
    {
        return $this->supplierOrderValidator->hasPendingOrders($supplierId->getValue());
    }
}
