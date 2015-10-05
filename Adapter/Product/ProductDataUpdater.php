<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Core\Foundation\Exception\WarningException;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

/**
 * This class will update/insert/delete data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class ProductDataUpdater
{
    /**
     * Activate or deactivate a list of products.
     *
     * @param array $productListId The ID list of products to (de)activate
     * @param boolean $activate True to activate, false to deactivate.
     * @throws WarningException If an error occured during update (not blocking since its just activation flag)
     * @return boolean True when succeed.
     */
    public function activateProductIdList(array $productListId, $activate = true)
    {
        if (count($productListId) < 1) {
            throw new DevelopmentErrorException('ProductDataUpdater->activateProductIdList() should always receive at least one ID. Zero given.');
        }

        $failedIdList = array();
        foreach ($productListId as $productId) {
            $product = new \Product($productId);
            if (!\Validate::isLoadedObject($product)) {
                $failedIdList[] = $productId;
                continue;
            }
            $product->active = ($activate?1:0);
            $result = $product->update();
        }

        if (count($failedIdList) > 0) {
            throw new WarningException('Cannot change activation state on many requested products.', $failedIdList);
        }
        return true;
    }

    /**
     * Do a safe delete on given product IDs
     *
     * @param array $productListId The ID list of products to delete
     * @throws WarningException If deletion failed (some normal cases can brings this, it's not a Development error)
     * @return boolean
     */
    public function deleteProductIdList(array $productIdList)
    {
        if (count($productIdList) < 1) {
            throw new DevelopmentErrorException('ProductDataUpdater->deleteProductIdList() should always receive at least one ID. Zero given.');
        }

        $failedIdList = $productIdList; // Since we have just one call to delete all, cannot have distinctive fails.
        $result = (new \Product())->deleteSelection($productIdList);

        if ($result === 0) {
            throw new WarningException('Cannot delete many requested products.', $failedIdList);
        }
        return true;
    }

    /**
     * Do a safe delete on given product object
     *
     * @param \Product $product The product to delete
     * @throws WarningException If deletion failed (some normal cases can brings this, it's not a Development error)
     * @return boolean
     */
    public function deleteProduct(\Product $product)
    {
        // dumb? no: delete() makes a lot of things, and can reject deletion in specific cases.
        $result = $product->delete();

        if ($result === 0) {
            throw new WarningException('Cannot delete the requested product.', $productId);
        }
        return true;
    }

    /**
     * Duplicates the given product, and returns the new ID.
     *
     * Code comes from Legacy controller!
     *
     * @param \Product $product The original product
     * @return integer The new product ID (duplicate)
     */
    public function duplicateProduct(\Product $product)
    {
        $id_product_old = $product->id;
        if (empty($product->price) && \Shop::getContext() == \Shop::CONTEXT_GROUP) {
            $shops = \ShopGroup::getShopsFromGroup(\Shop::getContextShopGroupID());
            foreach ($shops as $shop) {
                if ($product->isAssociatedToShop($shop['id_shop'])) {
                    $product_price = new \Product($id_product_old, false, null, $shop['id_shop']);
                    $product->price = $product_price->price;
                }
            }
        }

        unset($product->id);
        unset($product->id_product);
        $product->indexed = 0;
        $product->active = 0;

        if ($product->add()
            && \Category::duplicateProductCategories($id_product_old, $product->id)
            && \Product::duplicateSuppliers($id_product_old, $product->id)
            && ($combination_images = \Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && \GroupReduction::duplicateReduction($id_product_old, $product->id)
            && \Product::duplicateAccessories($id_product_old, $product->id)
            && \Product::duplicateFeatures($id_product_old, $product->id)
            && \Product::duplicateSpecificPrices($id_product_old, $product->id)
            && \Pack::duplicate($id_product_old, $product->id)
            && \Product::duplicateCustomizationFields($id_product_old, $product->id)
            && \Product::duplicateTags($id_product_old, $product->id)
            && \Product::duplicateDownload($id_product_old, $product->id)) {
            if ($product->hasAttributes()) {
                \Product::updateDefaultAttribute($product->id);
            }

            if (!\Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                throw new WarningException('An error occurred while copying images.');
            } else {
                \Hook::exec('actionProductAdd', array('id_product' => (int)$product->id, 'product' => $product));
                if (in_array($product->visibility, array('both', 'search')) && \Configuration::get('PS_SEARCH_INDEXATION')) {
                    \Search::indexation(false, $product->id);
                }
                return $product->id;
            }
        } else {
            throw new ErrorException('An error occurred while creating an object.');
        }
    }
}
