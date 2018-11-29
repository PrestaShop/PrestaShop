<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Db;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierAddressProvider;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierOrderValidator;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

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
     * Starts mysql transaction.
     */
    protected function startTransaction()
    {
        Db::getInstance()->execute('START TRANSACTION;');
    }

    /**
     * Cancels mysql transaction which prevents from adding, updating, deleting unwanted data.
     */
    protected function rollbackTransaction()
    {
        Db::getInstance()->execute('ROLLBACK;');
    }

    /**
     * Commits the transaction.
     */
    protected function commitTransaction()
    {
        Db::getInstance()->execute('COMMIT;');
    }

    /**
     * Deletes product supplier relation.
     *
     * @param SupplierId $supplierId
     *
     * @return bool
     */
    protected function deleteProductSupplierRelation(SupplierId $supplierId)
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
    protected function deleteSupplierAddress(SupplierId $supplierId)
    {
        $supplierAddressId = $this->supplierAddressProvider->getIdBySupplier($supplierId->getValue());

        $address = new Address($supplierAddressId);

        if ($address->id) {
            $address->deleted = true;
            return $address->update();
        }

        return true;
    }
}
