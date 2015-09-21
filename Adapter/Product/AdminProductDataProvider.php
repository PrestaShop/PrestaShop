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

use PrestaShop\PrestaShop\Adapter\AbstractAdminDataProvider;

/**
 * This class will provide data from DB / ORM about Products for the Admin interface.
 */
class AdminProductDataProvider extends AbstractAdminDataProvider
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
        $paramsOut = array_merge_recursive($persistedParams, (array)$paramsIn);

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
     * @param array $get filter params values to take into acount (often comes from GET data).
     * @return array A list of products, as a collection of legacy Product objects.
     */
    public function getCatalogProductList($offset, $limit, $orderBy, $orderWay, $get = array())
    {
        $filterParams = $this->combinePersistentCatalogProductFilter($get);

        $idShop = \Context::getContext()->shop->id;
        $idLang = \Context::getContext()->language->id;

        $sqlSelect = array(
            'id_product' => array('table' => 'p', 'field' => 'id_product'),
            'reference' => array('table' => 'p', 'field' => 'reference'),
            'price' => array('table' => 'p', 'field' => 'price'),
            'id_shop_default' => array('table' => 'p', 'field' => 'id_shop_default'),
            'is_virtual' => array('table' => 'p', 'field' => 'is_virtual'),
            'name' => array('table' => 'pl', 'field' => 'name'),
            'active' => array('table' => 'sa', 'field' => 'active'),
            'price' => array('table' => 'sa', 'field' => 'price'),
            'shopname' => array('table' => 'shop', 'field' => 'name'),
            'id_image' => array('table' => 'image_shop', 'field' => 'id_image'),
            'name_category' => array('table' => 'cl', 'field' => 'name'),
            'price_final' => '0',
            'nb_downloadable' => array('table' => 'pd', 'field' => 'nb_downloadable'),
            'sav_quantity' => array('table' => 'sav', 'field' => 'quantity'),
            'sav_quantity' => array('table' => 'sav', 'field' => 'quantity'),
            'badge_danger' => 'IF(sav.`quantity`<=0, 1, 0)'
        );
        $sqlTable = array(
            'p' => 'product',
            'pl' => array(
                'table' => 'product_lang',
                'join' => 'LEFT JOIN',
                'on' => 'pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.$idLang.' AND pl.`id_shop` = '.$idShop
            ),
            'sav' => array(
                'table' => 'stock_available',
                'join' => 'LEFT JOIN',
                'on' => 'sav.`id_product` = p.`id_product` AND sav.`id_product_attribute` = 0 AND sav.id_shop_group = 1 AND sav.id_shop = 0' // FIXME, +anomalie id_shop ?
            ),
            'sa' => array(
                'table' => 'product_shop',
                'join' => 'JOIN',
                'on' => 'p.`id_product` = sa.`id_product` AND sa.id_shop = '.$idShop
            ),
            'cl' => array(
                'table' => 'category_lang',
                'join' => 'LEFT JOIN',
                'on' => 'sa.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.$idLang.' AND cl.id_shop = '.$idShop
            ),
            'shop' => array(
                'table' => 'shop',
                'join' => 'LEFT JOIN',
                'on' => 'shop.id_shop = '.$idShop
            ),
            'image_shop' => array(
                'table' => 'image_shop',
                'join' => 'LEFT JOIN',
                'on' => 'image_shop.`id_product` = p.`id_product` AND image_shop.`cover` = 1 AND image_shop.id_shop = '.$idShop // FIXME: cover
            ),
            'i' => array(
                'table' => 'image',
                'join' => 'LEFT JOIN',
                'on' => 'i.`id_image` = image_shop.`id_image`'
            ),
            'pd' => array(
                'table' => 'product_download',
                'join' => 'LEFT JOIN',
                'on' => 'pd.`id_product` = p.`id_product`'
            )
        );
        $sqlWhere = array(
//             'AND', // opt
//             array(
//                 'AND', // opt
//                 '1'
//             ),
//             array(
//                 'OR',
//                 '2',
//                 '3'
//             ),
//             array(
//                 'AND', // opt
//                 array(
//                     'OR',
//                     '4',
//                     '5'
//                 ),
//                 array(
//                     '6',
//                     '7'
//                 )
//             )
        ); // TODO
        $sqlOrder = array($orderBy.' '.$orderWay);
        $sqlLimit = $offset.', '.$limit;

        $sql = $this->compileSqlQuery($sqlSelect, $sqlTable, $sqlWhere, $sqlOrder, $sqlLimit);
        $products = \Db::getInstance()->executeS($sql, true, false);
        $total = \Db::getInstance()->executeS('SELECT FOUND_ROWS();', true, false);
        $total = $total[0]['FOUND_ROWS()'];

        // post treatment
        foreach ($products as &$product) {
            $product['price'] = \Tools::displayPrice($product['price']);
            $product['total'] = $total;
        }
        // FIXME: format columns like CLDR and others

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
