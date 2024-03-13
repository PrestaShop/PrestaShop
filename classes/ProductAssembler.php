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
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;

/**
 * This class is responsible for enriching product data by all required fields
 * in a performant way, before it goes into ProductLazyArray or ProductListingLazyArray.
 *
 * If you want to enrich a whole list of products, use assembleProducts method to get the data in one query.
 *
 * Currently, the data is passing through Product::getProductProperties also, but this step should be removed
 * and all data from getProductProperties loaded on demand in the lazy arrays.
 */
class ProductAssemblerCore
{
    private $context;
    private $searchContext;

    /**
     * ProductAssemblerCore constructor.
     *
     * @param \Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->searchContext = new ProductSearchContext($context);
    }

    /**
     * Add missing product fields.
     *
     * @param array $rawProduct
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    private function addMissingProductFields(array $rawProduct): array
    {
        // If there is no ID product provided, return the original data
        if (empty($rawProduct['id_product'])) {
            return $rawProduct;
        }

        $sql = $this->getSqlQueryProductFields([(int) $rawProduct['id_product']]);
        $rows = Db::getInstance()->executeS($sql);
        if (empty($rows)) {
            return $rawProduct;
        }

        return array_merge($rows[0], $rawProduct);
    }

    /**
     * Add missing product fields to multiple products.
     *
     * @param array $rawProducts
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    private function addMissingProductFieldsForMultipleProducts(array $rawProducts): array
    {
        // Get product IDs we want to retrieve from database
        $productIds = array_column($rawProducts, 'id_product');

        // If there were no product IDs provided or somebody passed an empty array,
        // return the original data
        if (empty($productIds)) {
            return $rawProducts;
        }

        // Retrieve data and reassign them to new array by their key
        $productData = [];
        $sql = $this->getSqlQueryProductFields($productIds);
        $rows = Db::getInstance()->executeS($sql);
        foreach ($rows as $row) {
            $productData[(int) $row['id_product']] = $row;
        }

        // Use this data to enrich the products and return it
        foreach ($rawProducts as &$rawProduct) {
            if (isset($productData[$rawProduct['id_product']])) {
                $rawProduct = array_merge($productData[$rawProduct['id_product']], $rawProduct);
            }
        }

        return $rawProducts;
    }

    /**
     * Return the SQL query to get all product fields.
     *
     * @param array $productIds
     *
     * @return string
     */
    private function getSqlQueryProductFields(array $productIds): string
    {
        // Get basic configuration
        $idShop = $this->searchContext->getIdShop();
        $idShopGroup = $this->searchContext->getIdShopGroup();
        $isStockSharingBetweenShopGroupEnabled = $this->searchContext->isStockSharingBetweenShopGroupEnabled();
        $idLang = $this->searchContext->getIdLang();
        $prefix = _DB_PREFIX_;

        $nbDaysNewProduct = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }
        $now = date('Y-m-d') . ' 00:00:00';

        $sql = "SELECT
                    p.*,
                    ps.*,
                    pl.*,
                    sa.out_of_stock,
                    IFNULL(sa.quantity, 0) as quantity,
                    (DATEDIFF(
                        p.`published_date`,
                        DATE_SUB(
                            '$now',
                            INTERVAL $nbDaysNewProduct DAY
                        )
                    ) > 0) as new
                FROM {$prefix}product p
                LEFT JOIN {$prefix}product_lang pl
                    ON pl.id_product = p.id_product
                    AND pl.id_shop = $idShop
                    AND pl.id_lang = $idLang
                LEFT JOIN {$prefix}stock_available sa ";

        if ($isStockSharingBetweenShopGroupEnabled) {
            $sql .= "  ON sa.id_product = p.id_product
			        AND sa.id_shop = 0
                    AND sa.id_product_attribute = 0
			        AND sa.id_shop_group = $idShopGroup ";
        } else {
            $sql .= "  ON sa.id_product = p.id_product
                    AND sa.id_product_attribute = 0
			        AND sa.id_shop = $idShop ";
        }
        $sql .= "LEFT JOIN {$prefix}product_shop ps
			        ON ps.id_product = p.id_product
			        AND ps.id_shop = $idShop
                WHERE p.id_product IN (" . implode(',', $productIds) . ')';

        return $sql;
    }

    /**
     * Get basic product data for single product.
     * The only required property is id_product.
     * If some data were already provided in $rawProduct, it won't be overwritten.
     *
     * @param array $rawProduct
     *
     * @return mixed
     *
     * @throws PrestaShopDatabaseException
     */
    public function assembleProduct(array $rawProduct)
    {
        $enrichedProduct = $this->addMissingProductFields($rawProduct);

        return Product::getProductProperties(
            $this->searchContext->getIdLang(),
            $enrichedProduct,
            $this->context
        );
    }

    /**
     * Get basic product data for multiple products.
     * The only required property for each product is id_product.
     * If some data were already provided in $rawProducts, it won't be overwritten.
     *
     * @param array $rawProducts Array with multiple products
     *
     * @return mixed
     *
     * @throws PrestaShopDatabaseException
     */
    public function assembleProducts(array $rawProducts)
    {
        $enrichedProducts = $this->addMissingProductFieldsForMultipleProducts($rawProducts);

        foreach ($enrichedProducts as &$product) {
            $product = Product::getProductProperties(
                $this->searchContext->getIdLang(),
                $product,
                $this->context
            );
        }

        return $enrichedProducts;
    }
}
