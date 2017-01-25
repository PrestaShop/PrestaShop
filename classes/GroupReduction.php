<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class GroupReductionCore
 */
class GroupReductionCore extends ObjectModel
{
    public $id_group;
    public $id_category;
    public $reduction;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'group_reduction',
        'primary' => 'id_group_reduction',
        'fields' => array(
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_category' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'reduction' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
        ),
    );

    protected static $reduction_cache = array();

    /**
     * Adds current GroupReduction as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the GroupReduction has been successfully added
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        return (parent::add($autoDate, $nullValues) && $this->setInternalCache());
    }

    /**
     * Updates the current GroupReduction in the database
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the GroupReduction has been successfully updated
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        return (parent::update($nullValues) && $this->updateInternalCache());
    }

    /**
     * Deletes current GroupReduction from the database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */
    public function delete()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int) $this->id_category
        );

        $ids = array();
        foreach ($products as $row) {
            $ids[] = $row['id_product'];
        }

        if ($ids) {
            Db::getInstance()->delete('product_group_reduction_cache', 'id_product IN ('.implode(', ', $ids).')');
        }

        return (parent::delete());
    }

    /**
     * @deprecated 1.7.0
     */
    protected function _clearCache()
    {
        return $this->clearInternalCache();
    }

    /**
     * Clear internal cache
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function clearInternalCache()
    {
        return Db::getInstance()->delete('product_group_reduction_cache', 'id_group = '.(int) $this->id_group);
    }

    /**
     * @return bool
     *
     * @deprecated 1.7.0
     */
    protected function _setCache()
    {
        return $this->setInternalCache();
    }

    /**
     * Set internal cache
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function setInternalCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int) $this->id_category
        );

        $values = array();
        foreach ($products as $row) {
            $values[] = '('.(int) $row['id_product'].', '.(int) $this->id_group.', '.(float) $this->reduction.')';
        }

        if (count($values)) {
            $query = 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`)
			VALUES '.implode(', ', $values).' ON DUPLICATE KEY UPDATE
			`reduction` = IF(VALUES(`reduction`) > `reduction`, VALUES(`reduction`), `reduction`)';

            return (Db::getInstance()->execute($query));
        }

        return true;
    }

    /**
     * @deprecated 1.7.0
     */
    protected function _updateCache()
    {
        return $this->updateInternalCache();
    }

    /**
     * Update internal cache
     *
     * @return bool
     *
     * @since 1.7.0
     */
    protected function updateInternalCache()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_product` cp
			WHERE cp.`id_category` = '.(int) $this->id_category,
        false);

        $ids = array();
        foreach ($products as $product) {
            $ids[] = $product['id_product'];
        }

        $result = true;
        if ($ids) {
            $result &= Db::getInstance()->update('product_group_reduction_cache', array(
                'reduction' => (float) $this->reduction,
            ), 'id_product IN('.implode(', ', $ids).') AND id_group = '.(int) $this->id_group);
        }

        return $result;
    }

    /**
     * Set Group reductions
     *
     * @param int $idGroup Group ID
     * @param int $idLang  Language ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getGroupReductions($idGroup, $idLang)
    {
        $lang = $idLang.Shop::addSqlRestrictionOnLang('cl');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction`, gr.`id_group`, gr.`id_category`, gr.`reduction`, cl.`name` AS category_name
			FROM `'._DB_PREFIX_.'group_reduction` gr
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cl.`id_category` = gr.`id_category` AND cl.`id_lang` = '.(int) $lang.')
			WHERE `id_group` = '.(int) $idGroup

        );
    }

    /**
     * Get Value for Product
     *
     * @param int $idProduct Product ID
     * @param int $idGroup   Group ID
     *
     * @return int|mixed
     */
    public static function getValueForProduct($idProduct, $idGroup)
    {
        if (!Group::isFeatureActive()) {
            return 0;
        }

        if (!isset(self::$reduction_cache[$idProduct.'-'.$idGroup])) {
            self::$reduction_cache[$idProduct.'-'.$idGroup] = Db::getInstance()->getValue('
			SELECT `reduction`
			FROM `'._DB_PREFIX_.'product_group_reduction_cache`
			WHERE `id_product` = '.(int) $idProduct.' AND `id_group` = '.(int) $idGroup);
        }
        // Should return string (decimal in database) and not a float
        return self::$reduction_cache[$idProduct.'-'.$idGroup];
    }

    /**
     * Does the Group exit?
     *
     * @param int $idGroup
     * @param int $idCategory
     *
     * @return bool
     */
    public static function doesExist($idGroup, $idCategory)
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_group`
		FROM `'._DB_PREFIX_.'group_reduction`
		WHERE `id_group` = '.(int) $idGroup.' AND `id_category` = '.(int) $idCategory);
    }

    /**
     * Get groups by Category ID
     *
     * @param int $idCategory Category ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getGroupsByCategoryId($idCategory)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group` as id_group, gr.`reduction` as reduction, id_group_reduction
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int) $idCategory
        );
    }

    /**
     * Get GroupReduction by Category ID
     *
     * @param int $idCategory Category ID
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public static function getGroupsReductionByCategoryId($idCategory)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT gr.`id_group_reduction` as id_group_reduction, id_group
			FROM `'._DB_PREFIX_.'group_reduction` gr
			WHERE `id_category` = '.(int) $idCategory
        );
    }

    /**
     * Set Product reduction
     *
     * @param int      $idProduct
     * @param int|null $id_group
     * @param int|null $id_category
     * @param mixed    $reduction
     *
     * @return bool
     */
    public static function setProductReduction($idProduct)
    {
        $res = true;
        GroupReduction::deleteProductReduction((int) $idProduct);

        $categories = Product::getProductCategories((int) $idProduct);

        if ($categories) {
            foreach ($categories as $category) {
                $reductions = GroupReduction::getGroupsByCategoryId((int) $category);
                if ($reductions) {
                    foreach ($reductions as $reduction) {
                        $currentGroupReduction = new GroupReduction((int) $reduction['id_group_reduction']);
                        $res &= $currentGroupReduction->setInternalCache();
                    }
                }
            }
        }

        return $res;
    }

    /**
     * Delete Product reduction
     *
     * @param int $idProduct Product ID
     *
     * @return bool
     */
    public static function deleteProductReduction($idProduct)
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` WHERE `id_product` = '.(int) $idProduct;
        if (Db::getInstance()->execute($query) === false) {
            return false;
        }

        return true;
    }

    /**
     * Duplicate GroupReduction
     *
     * @param int $idProductOld Old Product ID
     * @param int $idProduct    Product ID
     *
     * @return bool
     */
    public static function duplicateReduction($idProductOld, $idProduct)
    {
        $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT pgr.`id_product`, pgr.`id_group`, pgr.`reduction`
			FROM `'._DB_PREFIX_.'product_group_reduction_cache` pgr
			WHERE pgr.`id_product` = '.(int) $idProductOld
        );

        if (!$res) {
            return true;
        }

        $query = '';

        foreach ($res as $row) {
            $query .= 'INSERT INTO `'._DB_PREFIX_.'product_group_reduction_cache` (`id_product`, `id_group`, `reduction`) VALUES ';
            $query .= '('.(int) $idProduct.', '.(int) $row['id_group'].', '.(float) $row['reduction'].') ON DUPLICATE KEY UPDATE `reduction` = '.(float) $row['reduction'].';';
        }

        return Db::getInstance()->execute($query);
    }

    /**
     * Delete Category ID from GroupReduction table
     *
     * @param int $idCategory Category ID
     *
     * @return bool
     */
    public static function deleteCategory($idCategory)
    {
        $query = 'DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_category` = '.(int) $idCategory;
        if (Db::getInstance()->execute($query) === false) {
            return false;
        }

        return true;
    }
}
