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

/**
 * This class will provide data from DB / ORM about Products for the Admin interface.
 */
class AdminProductDataProvider
{
    /**
     * Combines new filter values with old ones (persisted), then persists the combination and returns it.
     *
     * @param array $paramsIn New filter params values to take into acount. If not given, the method will simply return persisted values.
     * @return array The new filter params values
     */
    public function combinePersistentCatalogProductFilter($paramsIn = array())
    {
        $paramsOut = array();

        // retrieve persisted filter parameters
        $persistedParams = array(); // TODO

        // merge with new values
        $paramsOut = array_merge_recursive($persistedParams, $paramsIn);

        // persist new values
        // TODO: $paramsOut

        // return new values
        return $paramsOut;
    }

    /**
     * Returns a collection of products, using default language, currency and others, from Context.
     *
     * @param integer $offset
     * @param integer $limit
     * @param string $orderBy Field name to sort during SQL query
     * @param string $orderWay 'asc' or 'desc'
     * @return array A list of products, as a collection of legacy Product objects.
     */
    public function getCatalogProductList($offset, $limit, $orderBy, $orderWay, $get = array())
    {
        $filterParams = $this->combinePersistentCatalogProductFilter($get);

        // FIXME: to optimize! Only needed columns, well filtered, and format columns like CLDR and others. FAIRE LA REQUETE ICI ! +SQL_CALC_FOUND_ROWS

        $where = '';
        $sql = '
          SELECT SQL_CALC_FOUND_ROWS AS `total`,
              p.`id_product`, p.`reference`, p.`price` AS `price`, p.`id_shop_default`, p.`is_virtual`,
              pl.`name` AS `name`,
              sa.`active` AS `active`, sa.`price`, sa.`active`,
              shop.`name` AS `shopname`,
              image_shop.`id_image` AS `id_image`,
              cl.`name` AS `name_category`,
              0 AS `price_final`,
              pd.`nb_downloadable`,
              sav.`quantity` AS `sav_quantity`, IF(sav.`quantity`<=0, 1, 0) AS `badge_danger`
          FROM `ps_product` p
          LEFT JOIN `ps_product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = 1 AND pl.`id_shop` = 1)
          LEFT JOIN `ps_stock_available` sav ON (sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop_group = 1 AND sav.id_shop = 0 )
          JOIN `ps_product_shop` sa ON (p.`id_product` = sa.`id_product` AND sa.id_shop = 1)
          LEFT JOIN `ps_category_lang` cl ON (sa.`id_category_default` = cl.`id_category` AND pl.`id_lang` = cl.`id_lang` AND cl.id_shop = 1)
          LEFT JOIN `ps_shop` shop ON (shop.id_shop = 1)
          LEFT JOIN `ps_image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = 1)
          LEFT JOIN `ps_image` i ON (i.`id_image` = image_shop.`id_image`)
          LEFT JOIN `ps_product_download` pd ON (pd.`id_product` = p.`id_product`)
          WHERE '.$where.'
          ORDER BY '.$orderBy.' '.$orderWay.'
          LIMIT '.$offset.', '.$limit;
        //$products = Db::getInstance()->executeS($sql, true, false);

        $idLang = \Context::getContext()->language->id;
        $products = \Product::getProducts($idLang, $offset, $limit, $orderBy, $orderWay);
        foreach ($products as &$product) {
            $product['price'] = \Tools::displayPrice($product['price']);
        }
        return $products;
    }

    /**
     * Translates new Core route parameters into their Legacy equivalent.
     *
     * @param array $coreParameters The new Core route parameters
     * @return array The URL parameters for Legacy URL (GETs)
     */
    public function mapLegacyParametersProductForm($coreParameters = array())
    {
        $params = array();
        if ($coreParameters['id_product'] == 'new') {
            $params['addproduct'] = 1;
        } else {
            $params['updateproduct'] = 1;
            $params['id_product'] = $coreParameters['id_product'];
        }
        return $params;
    }
}
