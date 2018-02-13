<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Attribute;

use Product;
use Attribute;
use Combination;
use Db;
use Shop;
use Context;

/**
 * This class will provide data from DB / ORM about Attributes
 */
class AttributeDataProvider
{
    /**
     * Get all attributes for a given language
     *
     * @param int $id_lang Language id
     * @param bool $not_null Get only not null fields if true
     *
     * @return array Attributes
     */
    public static function getAttributes($id_lang, $not_null = false)
    {
        return Attribute::getAttributes($id_lang, $not_null);
    }


    /**
     * Get all attributes ids for a given group
     *
     * @param int $id_group Attribute group id
     * @param bool $not_null Get only not null fields if true
     * @return array Attributes
     */
    public static function getAttributeIdsByGroup($id_group, $not_null = false)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $result = Db::getInstance()->executeS('
			SELECT DISTINCT a.`id_attribute`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE ag.`id_attribute_group` = '.(int)$id_group.'
			'.($not_null ? 'AND a.`id_attribute` IS NOT NULL' : '').'
			ORDER BY a.`position` ASC
		');

        return array_map(function ($a) {
            return $a['id_attribute'];
        }, $result);
    }

    /**
     * Get combination for a product
     *
     * @param int $idProduct
     *
     * @return array Combinations
     */
    public function getProductCombinations($idProduct)
    {
        $context = Context::getContext();

        //get product
        $product = new Product((int)$idProduct, false);
        if (!is_object($product) || empty($product->id)) {
            return false;
        }

        $allCombinations = $product->getAttributeCombinations(1, false);
        $allCombinationsIds = array_map(function ($o) {
            return $o['id_product_attribute'];
        }, $allCombinations);

        $combinations = [];
        foreach ($allCombinationsIds as $combinationId) {
            $combinations[] = $product->getAttributeCombinationsById($combinationId, $context->employee->id_lang)[0];
        }

        return $combinations;
    }

    /**
     * Get combination images ids
     *
     * @param int $idAttribute
     *
     * @return array
     */
    public function getImages($idAttribute)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT a.`id_image` as id
			FROM `'._DB_PREFIX_.'product_attribute_image` a
			'.Shop::addSqlAssociation('product_attribute', 'a').'
			WHERE a.`id_product_attribute` = '.(int)$idAttribute.'
		');
    }
}
