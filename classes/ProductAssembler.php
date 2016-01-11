<?php

use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchContext;

class ProductAssemblerCore
{
    private $context;
    private $searchContext;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->searchContext = new ProductSearchContext($context);
    }

    private function addMissingProductFields(array $rawProduct)
    {
        $id_shop = (int)$this->searchContext->getIdShop();
        $id_lang = (int)$this->searchContext->getIdLang();
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

    public function assembleProduct(array $rawProduct)
    {
        $enrichedProduct = $this->addMissingProductFields($rawProduct);

        return Product::getProductProperties(
            $this->searchContext->getIdLang(),
            $enrichedProduct,
            $this->context
        );
    }
}
