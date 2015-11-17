<?php

use PrestaShop\PrestaShop\Core\Business\Product\ProductPresenter;
use PrestaShop\PrestaShop\Core\Business\Product\ProductPresentationSettings;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchContext;

abstract class ProductListingFrontControllerCore extends ProductPresentingFrontController
{
    private function addMissingProductFields(array $rawProduct)
    {
        $id_shop = (int)$this->getProductSearchContext()->getIdShop();
        $id_lang = (int)$this->getProductSearchContext()->getIdLang();
        $id_product = (int)$rawProduct['id_product'];
        $prefix = _DB_PREFIX_;

        $nb_days_new_product = (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nb_days_new_product)) {
            $nb_days_new_product = 20;
        }
        $now = date('Y-m-d') . ' 00:00:00';

        $sql = "SELECT
                    p.*,
                    pl.*,
                    (DATE_SUB('$now', INTERVAL $nb_days_new_product DAY) > 0) as new
                FROM {$prefix}product p
                INNER JOIN {$prefix}product_lang pl
                    ON pl.id_product = p.id_product
                    AND pl.id_shop = $id_shop
                    AND pl.id_lang = $id_lang
                    AND p.id_product = $id_product";

        $rows = Db::getInstance()->executeS($sql);
        return array_merge($rows[0], $rawProduct);
    }

    private function prepareProductForTemplate(array $rawProduct)
    {
        $enrichedProduct = $this->addMissingProductFields(
            $rawProduct
        );

        $product = Product::getProductProperties(
            $this->getProductSearchContext()->getIdLang(),
            $enrichedProduct,
            $this->context
        );

        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->presentForListing(
            $settings,
            $product,
            $this->context->language
        );
    }

    protected function prepareProductsForTemplate(array $products)
    {
        return array_map([$this, 'prepareProductForTemplate'], $products);
    }

    protected function getProductSearchContext()
    {
        return (new ProductSearchContext)
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
        ;
    }

    protected function runProductSearchQuery(ProductSearchQuery $query)
    {
        $provider = $this->getProductSearchProvider($query);
        return $provider->runQuery(
            $this->getProductSearchContext(),
            $query
        );
    }

    protected function assignProductSearchVariables()
    {
        $result = $this->runProductSearchQuery(
            $this->getProductSearchQuery()
        );

        $products = $this->prepareProductsForTemplate(
            $result->getProducts()
        );

        $this->context->smarty->assign([
            'products'      => $products,
            'sort_options'  => [],
            'pagination'    => []
        ]);
    }

    abstract protected function getProductSearchQuery();
    abstract protected function getProductSearchProvider(ProductSearchQuery $query);
}
