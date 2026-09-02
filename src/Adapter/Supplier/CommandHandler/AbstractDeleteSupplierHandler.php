<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Supplier\CommandHandler;

use Address;
use Db;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierAddressProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\CannotDeleteSupplierAddressException;
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
     * @var string
     */
    private $dbPrefix;

    /**
     * @var SupplierAddressProvider
     */
    private $supplierAddressProvider;

    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param SupplierAddressProvider $supplierAddressProvider
     * @param ProductSupplierUpdater $productSupplierUpdater
     * @param string $dbPrefix
     */
    public function __construct(
        SupplierAddressProvider $supplierAddressProvider,
        ProductSupplierUpdater $productSupplierUpdater,
        string $dbPrefix
    ) {
        $this->dbPrefix = $dbPrefix;
        $this->supplierAddressProvider = $supplierAddressProvider;
        $this->productSupplierUpdater = $productSupplierUpdater;
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

            if (false === $this->deleteProductSupplierRelation($supplierId)) {
                throw new CannotDeleteSupplierProductRelationException(
                    sprintf(
                        'Unable to delete suppliers with id "%d" product relation from product_supplier table',
                        $supplierId->getValue()
                    )
                );
            }

            if (1 >= count($entity->getAssociatedShops()) && false === $this->deleteSupplierAddress($supplierId)) {
                throw new CannotDeleteSupplierAddressException(
                    sprintf(
                        'Unable to set deleted flag for supplier with id "%d" address',
                        $supplierId->getValue()
                    )
                );
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
        $removedRelations = Db::getInstance()->execute($sql);

        // Fetch all products which had this supplier as default
        $sql = 'SELECT id_product FROM `' . $this->dbPrefix . 'product` WHERE `id_supplier` = ' . $supplierId->getValue();
        $result = Db::getInstance()->executeS($sql);
        if (!empty($result)) {
            $orphanProductIds = [];
            foreach ($result as $product) {
                $orphanProductIds[] = new ProductId((int) $product['id_product']);
            }

            $this->productSupplierUpdater->resetSupplierAssociations($orphanProductIds);
        }

        return $removedRelations;
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
}
