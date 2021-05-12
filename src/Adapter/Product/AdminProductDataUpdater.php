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

namespace PrestaShop\PrestaShop\Adapter\Product;

use Category;
use Configuration;
use Db;
use GroupReduction;
use Image;
use Pack;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface;
use Product;
use Search;
use Shop;
use ShopGroup;
use Validate;

/**
 * This class will update/insert/delete data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class AdminProductDataUpdater implements ProductInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * Constructor. HookDispatcher is injected by Sf container.
     *
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function activateProductIdList(array $productListId, $activate = true)
    {
        if (count($productListId) < 1) {
            throw new \Exception('AdminProductDataUpdater->activateProductIdList() should always receive at least one ID. Zero given.', 5003);
        }

        $failedIdList = [];
        foreach ($productListId as $productId) {
            $product = new Product($productId);
            if (!Validate::isLoadedObject($product)
                || $product->validateFields(false, true) !== true
                || $product->validateFieldsLang(false, true) !== true) {
                $failedIdList[] = $productId;

                continue;
            }
            $product->active = (bool) $activate;
            $product->update();
            if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                Search::indexation(false, $product->id);
            }
            $this->hookDispatcher->dispatchWithParameters('actionProductActivation', ['id_product' => (int) $product->id, 'product' => $product, 'activated' => $activate]);
        }

        if (count($failedIdList) > 0) {
            throw new UpdateProductException('Cannot change activation state on many requested products', 5004);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProductIdList(array $productIdList)
    {
        if (count($productIdList) < 1) {
            throw new \Exception('AdminProductDataUpdater->deleteProductIdList() should always receive at least one ID. Zero given.', 5005);
        }

        $failedIdList = $productIdList; // Since we have just one call to delete all, cannot have distinctive fails.
        // Hooks: will trigger actionProductDelete multiple times
        $result = (new Product())->deleteSelection($productIdList);

        if ($result === 0) {
            throw new UpdateProductException('Cannot delete many requested products.', 5006);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function duplicateProductIdList(array $productIdList)
    {
        if (count($productIdList) < 1) {
            throw new \Exception('AdminProductDataUpdater->duplicateProductIdList() should always receive at least one ID. Zero given.', 5005);
        }

        $failedIdList = [];
        foreach ($productIdList as $productId) {
            try {
                $this->duplicateProduct($productId);
            } catch (\Exception $e) {
                $failedIdList[] = $productId;

                continue;
            }
        }

        if (count($failedIdList) > 0) {
            throw new UpdateProductException('Cannot duplicate many requested products', 5004);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProduct($productId)
    {
        $product = new Product($productId);
        if (!Validate::isLoadedObject($product)) {
            throw new \Exception('AdminProductDataUpdater->deleteProduct() received an unknown ID.', 5005);
        }

        // dumb? no: delete() makes a lot of things, and can reject deletion in specific cases.
        // Hooks: will trigger actionProductDelete
        $result = $product->delete();

        if ($result === false) {
            throw new UpdateProductException('Cannot delete the requested product.', 5007);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function duplicateProduct($productId, $namePattern = 'copy of %s')
    {
        //TODO : use the $namePattern var to input translated version of 'copy of %s', if translation requested.
        $product = new Product($productId);
        if (!Validate::isLoadedObject($product)) {
            throw new \Exception('AdminProductDataUpdater->duplicateProduct() received an unknown ID.', 5005);
        }

        if (($error = $product->validateFields(false, true)) !== true
            || ($error = $product->validateFieldsLang(false, true)) !== true) {
            throw new UpdateProductException(sprintf('Cannot duplicate this product: %s', $error));
        }

        $id_product_old = $product->id;
        if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
            $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
            foreach ($shops as $shop) {
                if ($product->isAssociatedToShop($shop['id_shop'])) {
                    $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                    $product->price = $product_price->price;
                }
            }
        }

        unset(
            $product->id,
            $product->id_product
        );

        $product->indexed = false;
        $product->active = false;

        // change product name to prefix it
        foreach ($product->name as $langKey => $oldName) {
            if (!preg_match('/^' . str_replace('%s', '.*', preg_quote($namePattern, '/') . '$/'), $oldName)) {
                $newName = sprintf($namePattern, $oldName);
                if (mb_strlen($newName, 'UTF-8') <= 127) {
                    $product->name[$langKey] = $newName;
                }
            }
        }

        if ($product->add()
            && Category::duplicateProductCategories($id_product_old, $product->id)
            && Product::duplicateSuppliers($id_product_old, $product->id)
            && ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            && Product::duplicateAccessories($id_product_old, $product->id)
            && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateSpecificPrices($id_product_old, $product->id)
            && Pack::duplicate($id_product_old, $product->id)
            && Product::duplicateCustomizationFields($id_product_old, $product->id)
            && Product::duplicatePrices($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateTaxes($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {
            if ($product->hasAttributes()) {
                Product::updateDefaultAttribute($product->id);
            }

            if (!Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                throw new UpdateProductException('An error occurred while copying images.', 5008);
            } else {
                $this->hookDispatcher->dispatchWithParameters('actionProductAdd', ['id_product_old' => $id_product_old, 'id_product' => (int) $product->id, 'product' => $product]);
                if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                    Search::indexation(false, $product->id);
                }

                return $product->id;
            }
        } else {
            if ($product->id !== null) {
                $product->delete();
            }
            throw new \Exception('An error occurred while creating an object.', 5009);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sortProductIdList(array $productList, $filterParams)
    {
        if (count($productList) < 2) {
            return false;
        }

        if (!isset($filterParams['filter_category'])) {
            throw new \Exception('Cannot sort when filterParams does not contains \'filter_category\'.', 5010);
        }

        foreach ($filterParams as $k => $v) {
            if ($v == '' || strpos($k, 'filter_') !== 0) {
                continue;
            }
            if ($k == 'filter_category') {
                continue;
            }

            throw new \Exception('Cannot sort when filterParams contains other filter than \'filter_category\'.', 5010);
        }

        $categoryId = $filterParams['filter_category'];
        $minPosition = min(array_values($productList));
        $productsIds = implode(',', array_map('intval', array_keys($productList)));

        /*
         * First request to update position on category_product
         */
        Db::getInstance()->query('SET @i := ' . (((int) $minPosition) - 1));
        $updatePositions = 'UPDATE `' . _DB_PREFIX_ . 'category_product` cp ' .
            'SET cp.`position` = (SELECT @i := @i + 1) ' .
            'WHERE cp.`id_category` = ' . (int) $categoryId . ' AND cp.`id_product` IN (' . $productsIds . ') ' .
            'ORDER BY FIELD(cp.`id_product`, ' . $productsIds . ')';
        Db::getInstance()->query($updatePositions);

        /**
         * Second request to update date_upd because
         * ORDER BY is not working on multi-tables update
         */
        $updateProducts = 'UPDATE `' . _DB_PREFIX_ . 'product` p ' .
            '' . Shop::addSqlAssociation('product', 'p') . ' ' .
            'SET ' .
            '    p.`date_upd` = "' . date('Y-m-d H:i:s') . '", ' .
            '    product_shop.`date_upd` = "' . date('Y-m-d H:i:s') . '" ' .
            'WHERE p.`id_product` IN (' . $productsIds . ') ';
        Db::getInstance()->query($updateProducts);

        return true;
    }
}
