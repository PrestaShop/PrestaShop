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
use PrestaShop\PrestaShop\Core\Business\Context;
use PrestaShop\PrestaShop\Core\Foundation\Exception\ErrorException;

/**
 * This class will provide data from DB / ORM about Products for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 *
 * TODO: rewrite presistence of filter parameters.
 */
class AdminProductDataProvider extends AbstractAdminDataProvider
{
    /**
     * Will retrieve set of parameters from persistence, for product filters.
     *
     * @param string $prefix
     * @return array
     */
    public function getPersistedFilterParameters($prefix = '')
    {
        /** @var \CookieCore $legacyCookie */
        $legacyCookie = \Context::getContext()->cookie;
        return array(
            $prefix.'filter_category' => $legacyCookie->id_category_products_filter,
            $prefix.'filter_column_id_product' => $legacyCookie->productsproductFilter_id_product,
            $prefix.'filter_column_name' => $legacyCookie->__get('productsproductFilter_b!name'),
            $prefix.'filter_column_reference' => $legacyCookie->productsproductFilter_reference,
            $prefix.'filter_column_name_category' => $legacyCookie->__get('productsproductFilter_cl!name'),
            $prefix.'filter_column_price' => $legacyCookie->__get('productsproductFilter_a!price'),
            $prefix.'filter_column_sav_quantity' => $legacyCookie->__get('productsproductFilter_sav!quantity'),
            $prefix.'filter_column_active' => $legacyCookie->__get('productsproductFilter_active'),
        );
    }

    /**
     * Will persist set of parameters for product filters.
     *
     * @param array $parameters
     * @return array
     */
    public function persistFilterParameters(array $parameters)
    {
        /** @var \CookieCore $legacyCookie */
        $legacyCookie = \Context::getContext()->cookie;

        if (isset($parameters['filter_category'])) {
            $legacyCookie->__set('id_category_products_filter', $parameters['filter_category']);
        } else {
            $legacyCookie->__unset('id_category_products_filter');
        }
        if (isset($parameters['filter_column_id_product'])) {
            $legacyCookie->__set('productsproductFilter_id_product', $parameters['filter_column_id_product']);
        } else {
            $legacyCookie->__unset('productsproductFilter_id_product');
        }
        if (isset($parameters['filter_column_name'])) {
            $legacyCookie->__set('productsproductFilter_b!name', $parameters['filter_column_name']);
        } else {
            $legacyCookie->__unset('productsproductFilter_b!name');
        }
        if (isset($parameters['filter_column_reference'])) {
            $legacyCookie->__set('productsproductFilter_reference', $parameters['filter_column_reference']);
        } else {
            $legacyCookie->__unset('productsproductFilter_reference');
        }
        if (isset($parameters['filter_column_name_category'])) {
            $legacyCookie->__set('productsproductFilter_cl!name', $parameters['filter_column_name_category']);
        } else {
            $legacyCookie->__unset('productsproductFilter_cl!name');
        }
        if (isset($parameters['filter_column_price'])) {
            $legacyCookie->__set('productsproductFilter_a!price', $parameters['filter_column_price']);
        } else {
            $legacyCookie->__unset('productsproductFilter_a!price');
        }
        if (isset($parameters['filter_column_sav_quantity'])) {
            $legacyCookie->__set('productsproductFilter_sav!quantity', $parameters['filter_column_sav_quantity']);
        } else {
            $legacyCookie->__unset('productsproductFilter_sav!quantity');
        }
        if (isset($parameters['filter_column_active'])) {
            $legacyCookie->__set('productsproductFilter_active', $parameters['filter_column_active']);
        } else {
            $legacyCookie->__unset('productsproductFilter_active');
        }
    }

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
        $persistedParams = $this->getPersistedFilterParameters();

        // merge with new values
        $paramsOut = array_merge($persistedParams, (array)$paramsIn);

        // persist new values
        $this->persistFilterParameters($paramsOut);

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
     * @param array $post filter params values to take into acount (often comes from POST data).
     * @return array A list of products, as a collection of legacy Product objects.
     */
    public function getCatalogProductList($offset, $limit, $orderBy, $orderWay, $post = array())
    {
        $filterParams = $this->combinePersistentCatalogProductFilter($post);

        $idShop = \Context::getContext()->shop->id;
        $idLang = \Context::getContext()->language->id;

        $sqlSelect = array(
            'id_product' => array('table' => 'p', 'field' => 'id_product', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
            'reference' => array('table' => 'p', 'field' => 'reference', 'filtering' => self::FILTERING_LIKE_BOTH),
            'price' => array('table' => 'p', 'field' => 'price', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
            'id_shop_default' => array('table' => 'p', 'field' => 'id_shop_default'),
            'is_virtual' => array('table' => 'p', 'field' => 'is_virtual'),
            'name' => array('table' => 'pl', 'field' => 'name', 'filtering' => self::FILTERING_LIKE_BOTH),
            'active' => array('table' => 'sa', 'field' => 'active', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
            'shopname' => array('table' => 'shop', 'field' => 'name'),
            'id_image' => array('table' => 'image_shop', 'field' => 'id_image'),
            'name_category' => array('table' => 'cl', 'field' => 'name', 'filtering' => self::FILTERING_LIKE_BOTH),
            'price_final' => '0',
            'nb_downloadable' => array('table' => 'pd', 'field' => 'nb_downloadable'),
            'sav_quantity' => array('table' => 'sav', 'field' => 'quantity', 'filtering' => self::FILTERING_EQUAL_NUMERIC),
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
            'c' => array(
                'table' => 'category',
                'join' => 'LEFT JOIN',
                'on' => 'c.`id_category` = cl.`id_category`'
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
             'AND', // opt
             1,
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
        );
        foreach ($filterParams as $filterParam => $filterValue) {
            if (!$filterValue) {
                continue;
            }
            if (strpos($filterParam, 'filter_column_') === 0) {
                $field = substr($filterParam, 14); // 'filter_column_' takes 14 chars
                $sqlWhere[] = $sqlSelect[$field]['table'].'.`'.$sqlSelect[$field]['field'].'` '.sprintf($sqlSelect[$field]['filtering'], $filterValue);
            } else {
                if ($filterParam == 'filter_category') {
                    $sqlWhere[] = array(
                        'AND',
                        'c.`nleft` >= (SELECT `nleft` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.$filterValue.')',
                        'c.`nright` <= (SELECT `nright` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.$filterValue.')'
                    );
                } else {
                    throw new DevelopmentErrorException('The filter \''.$filterParam.'\' is not known for Products!');
                }
            }
        }
        
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
