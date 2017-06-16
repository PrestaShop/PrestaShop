<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface;
use PrestaShopBundle\Exception\UpdateProductException;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Product;
use Validate;
use Shop;
use ShopGroup;
use Category;
use GroupReduction;
use Pack;
use Search;
use Db;
use Configuration;
use Image;

/**
 * This class will update/insert/delete data from DB / ORM about Product, for both Front and Admin interfaces.
 */
class AdminProductDataUpdater implements ProductInterface
{
    /**
     * @var HookDispatcher
     */
    private $hookDispatcher;

    /**
     * Constructor. HookDispatcher is injected by Sf container.
     *
     * @param HookDispatcher $hookDispatcher
     */
    public function __construct(HookDispatcher $hookDispatcher)
    {
        $this->hookDispatcher = $hookDispatcher;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::activateProductIdList()
     */
    public function activateProductIdList(array $productListId, $activate = true)
    {
        if (count($productListId) < 1) {
            throw new \Exception('AdminProductDataUpdater->activateProductIdList() should always receive at least one ID. Zero given.', 5003);
        }

        $failedIdList = array();
        foreach ($productListId as $productId) {
            $product = new Product($productId);
            if (!Validate::isLoadedObject($product)) {
                $failedIdList[] = $productId;
                continue;
            }
            $product->active = ($activate?1:0);
            $product->update();
            $this->hookDispatcher->dispatchForParameters('actionProductActivation', array('id_product' => (int)$product->id, 'product' => $product, 'activated' => $activate));
        }

        if (count($failedIdList) > 0) {
            throw new UpdateProductException('Cannot change activation state on many requested products', 5004);
        }
        return true;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::deleteProductIdList()
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

    /* (non-PHPdoc)
         * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::duplicateProductIdList()
         */
    public function duplicateProductIdList(array $productIdList)
    {
        if (count($productIdList) < 1) {
            throw new \Exception('AdminProductDataUpdater->duplicateProductIdList() should always receive at least one ID. Zero given.', 5005);
        }

        $failedIdList = array();
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

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::deleteProduct()
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

        if ($result === 0) {
            throw new UpdateProductException('Cannot delete the requested product.', 5007);
        }
        return true;
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::duplicateProduct()
     */
    public function duplicateProduct($productId, $namePattern = 'copy of %s')
    {
        //TODO : use the $namePattern var to input translated version of 'copy of %s', if translation requested.
        $product = new Product($productId);
        if (!Validate::isLoadedObject($product)) {
            throw new \Exception('AdminProductDataUpdater->duplicateProduct() received an unknown ID.', 5005);
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

        unset($product->id);
        unset($product->id_product);
        $product->indexed = 0;
        $product->active = 0;

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
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {
            if ($product->hasAttributes()) {
                Product::updateDefaultAttribute($product->id);
            }

            if (!Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                throw new UpdateProductException('An error occurred while copying images.', 5008);
            } else {
                $this->hookDispatcher->dispatchForParameters('actionProductAdd', array('id_product' => (int)$product->id, 'product' => $product));
                if (in_array($product->visibility, array('both', 'search')) && Configuration::get('PS_SEARCH_INDEXATION')) {
                    Search::indexation(false, $product->id);
                }
                return $product->id;
            }
        } else {
            throw new \Exception('An error occurred while creating an object.', 5009);
        }
    }

    /* (non-PHPdoc)
     * @see \PrestaShopBundle\Service\DataUpdater\Admin\ProductInterface::sortProductIdList()
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

        /* Sorting items on one page only, with ONE SQL UPDATE query,
         * then fixing bugs (duplicates and 0 values) on next pages with more queries, if needed.
         *
         * Most complicated case example:
         * We have to sort items from offset 5, limit 5, on total object count: 14
         * The previous AND the next pages MUST NOT be impacted but fixed if needed.
         * legend:  #<id>|P<position>
         *
         * Before sort:
         * #1|P2 #2|P4 #3|P5 #7|P8 #6|P9   #5|P10 #8|P11 #10|P13 #12|P14 #11|P15   #9|P16 #12|P18 #14|P19 #22|P24
         * (there is holes in positions)
         *
         * Sort request:
         *                                 #5|P?? #10|P?? #12|P?? #8|P?? #11|P??
         *
         * After sort:
         * (previous page unchanged)       (page to sort: sort and no duplicates) (the next pages MUST be shifted to avoid duplicates if any)
         *
         * Request input:
         *                               [#5]P10 [#10]P13 [#12]P14 [#8]P11 [#11]P15
         */
        $maxPosition = max(array_values($productList));
        $sortedPositions = array_values($productList);
        sort($sortedPositions); // new positions to update

        // avoid '0', starts with '1', so shift right (+1)
        if ($sortedPositions[1] === 0) {
            foreach ($sortedPositions as $k => $v) {
                $sortedPositions[$k] = $v + 1;
            }
        }

        // combine old positions with new position in an array
        $combinedOldNewPositions = array_combine(array_values($productList), $sortedPositions);
        ksort($combinedOldNewPositions); // (keys: old positions starting at '1', values: new positions)
        $positionsMatcher = array_replace(array_pad(array(), $maxPosition, 0), $combinedOldNewPositions); // pad holes with 0
        array_shift($positionsMatcher);// shift because [0] is not used in MySQL FIELD()
        $fields = implode(',', $positionsMatcher);

        // update current pages.
        $updatePositions = 'UPDATE `'._DB_PREFIX_.'category_product` cp
            INNER JOIN `'._DB_PREFIX_.'product` p ON (cp.`id_product` = p.`id_product`)
            '.Shop::addSqlAssociation('product', 'p').'
            SET cp.`position` = ELT(cp.`position`, '.$fields.'),
                p.`date_upd` = "'.date('Y-m-d H:i:s').'",
                product_shop.`date_upd` = "'.date('Y-m-d H:i:s').'"
            WHERE cp.`id_category` = '.(int)$categoryId.' AND cp.`id_product` IN ('.implode(',', array_map('intval', array_keys($productList))).')';

        Db::getInstance()->query($updatePositions);

        // Fixes duplicates on all pages
        Db::getInstance()->query('SET @i := 0');
        $selectPositions = 'UPDATE`'._DB_PREFIX_.'category_product` cp
            SET cp.`position` = (SELECT @i := @i + 1)
            WHERE cp.`id_category` = '.(int)$categoryId.'
            ORDER BY cp.`id_product` NOT IN ('.implode(',', array_map('intval', array_keys($productList))).'), cp.`position` ASC';
        Db::getInstance()->query($selectPositions);

        return true;
    }
}
