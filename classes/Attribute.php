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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AttributeCore extends ObjectModel
{
    /** @var int Group id which attribute belongs */
    public $id_attribute_group;

    /** @var string Name */
    public $name;
    public $color;
    public $position;
    public $default;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'attribute',
        'primary' => 'id_attribute',
        'multilang' => true,
        'fields' => array(
            'id_attribute_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'color' =>                array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            'position' =>            array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        )
    );

    protected $image_dir = _PS_COL_IMG_DIR_;

    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_option_values',
        'objectNodeName' => 'product_option_value',
        'fields' => array(
            'id_attribute_group' => array('xlink_resource'=> 'product_options'),
        )
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            $result = Db::getInstance()->executeS('SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_attribute = '.(int)$this->id);
            $products = array();

            foreach ($result as $row) {
                $combination = new Combination($row['id_product_attribute']);

                $new_request = Db::getInstance()->executeS('
                    SELECT id_product, default_on
                    FROM '._DB_PREFIX_.'product_attribute
                    WHERE id_product_attribute = '.(int)$row['id_product_attribute']);

                foreach ($new_request as $value) {
                    if ($value['default_on'] == 1) {
                        $products[] = $value['id_product'];
                    }
                }

                $combination->delete();
            }

            foreach ($products as $id_product) {
                $result = Db::getInstance()->executeS('
                    SELECT id_product_attribute
                    FROM '._DB_PREFIX_.'product_attribute
                    WHERE id_product = '.(int)$id_product.' LIMIT 1');

                foreach ($result as $row) {
                    $product = new Product((int)$id_product);

                    if (Validate::isLoadedObject($product)) {
                        $product->deleteDefaultAttributes();
                        $product->setDefaultAttribute($row['id_product_attribute']);
                    }
                }
            }

            // Delete associated restrictions on cart rules
            CartRule::cleanProductRuleIntegrity('attributes', $this->id);

            /* Reinitializing position */
            $this->cleanPositions((int)$this->id_attribute_group);
        }

        $return = parent::delete();

        if ($return) {
            Hook::exec('actionAttributeDelete', array('id_attribute' => $this->id));
        }

        return $return;
    }

    public function update($null_values = false)
    {
        $return = parent::update($null_values);

        if ($return) {
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));
        }

        return $return;
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = Attribute::getHigherPosition($this->id_attribute_group) + 1;
        }

        $return = parent::add($autodate, $null_values);

        if ($return) {
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));
        }

        return $return;
    }

    /**
     * Get all attributes for a given language
     *
     * @param int $id_lang Language id
     * @param bool $notNull Get only not null fields if true
     * @return array Attributes
     */
    public static function getAttributes($id_lang, $not_null = false)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
			SELECT DISTINCT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			'.Shop::addSqlAssociation('attribute', 'a').'
			'.($not_null ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL AND agl.`id_attribute_group` IS NOT NULL' : '').'
			ORDER BY agl.`name` ASC, a.`position` ASC
		');
    }

    public static function isAttribute($id_attribute_group, $name, $id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $result = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE al.`name` = \''.pSQL($name).'\' AND ag.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY agl.`name` ASC, a.`position` ASC
		');

        return ((int)$result > 0);
    }

    /**
     * Get quantity for a given attribute combination
     * Check if quantity is enough to deserve customer
     *
     * @param int $id_product_attribute Product attribute combination id
     * @param int $qty Quantity needed
     * @return bool Quantity is available or not
     */
    public static function checkAttributeQty($id_product_attribute, $quantity, Shop $shop = null)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        $result = StockAvailable::getQuantityAvailableByProduct(null, (int)$id_product_attribute, $shop->id);

        return ($result && ($quantity <= $result));
    }

    /**
     * Update array with veritable quantity
     *
     * @deprecated since 1.5.0
     * @param array &$product
     * @return bool
     */
    public static function updateQtyProduct(&$product)
    {
        Tools::displayAsDeprecated();

        $id_product = (int)$product['id_product'];
        $quantity = Attribute::getAttributeQty($id_product);

        if ($quantity !== false) {
            $product['quantity'] = (int)$quantity;

            return true;
        }

        return false;
    }

    /**
     * Return true if attribute is color type
     *
     * @acces public
     * @return bool
     */
    public function isColorAttribute()
    {
        $result = Db::getInstance()->getRow('
			SELECT `group_type`
			FROM `'._DB_PREFIX_.'attribute_group`
			WHERE `id_attribute_group` = (
				SELECT `id_attribute_group`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute` = '.(int)$this->id.')
			AND group_type = \'color\'');

        return ($result) ? Db::getInstance()->numRows() : false;
    }

    /**
     * Get minimal quantity for product with attributes quantity
     *
     * @acces public static
     * @param int $id_product_attribute
     * @return mixed Minimal Quantity or false
     */
    public static function getAttributeMinimalQty($id_product_attribute)
    {
        $minimal_quantity = Db::getInstance()->getValue('
			SELECT `minimal_quantity`
			FROM `'._DB_PREFIX_.'product_attribute_shop` pas
			WHERE `id_shop` = '.(int)Context::getContext()->shop->id.'
			AND `id_product_attribute` = '.(int)$id_product_attribute
        );

        return ($minimal_quantity > 1) ? (int)$minimal_quantity : false;
    }

    /**
     * Move an attribute inside its group
     * @param bool $way Up (1)  or Down (0)
     * @param int $position
     * @return bool Update result
     */
    public function updatePosition($way, $position)
    {
        $id_attribute_group = (int)Tools::getValue('id_attribute_group', $this->id_attribute_group);

        $sql = '
			SELECT a.`id_attribute`, a.`position`, a.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute` a
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY a.`position` ASC';

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($result as $attribute) {
            if ((int)$attribute['id_attribute'] == (int)$this->id) {
                $moved_attribute = $attribute;
            }
        }

        if (!isset($moved_attribute) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases

        $result1 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
                ? '> '.(int)$moved_attribute['position'].' AND `position` <= '.(int)$position
                : '< '.(int)$moved_attribute['position'].' AND `position` >= '.(int)$position).'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
        );

        $result2 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute` = '.(int)$moved_attribute['id_attribute'].'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
        );

        return ($result1 && $result2);
    }

    /**
     * Reorder attribute position in group $id_attribute_group.
     * Call it after deleting an attribute from a group.
     *
     * @param int $id_attribute_group
     * @param bool $use_last_attribute
     * @return bool $return
     */
    public function cleanPositions($id_attribute_group, $use_last_attribute = true)
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `'._DB_PREFIX_.'attribute` SET `position` = @i:=@i+1 WHERE';

        if ($use_last_attribute) {
            $sql .= ' `id_attribute` != '.(int)$this->id.' AND';
        }

        $sql .= ' `id_attribute_group` = '.(int)$id_attribute_group.' ORDER BY `position` ASC';

        $return = Db::getInstance()->execute($sql);
    }

    /**
     * getHigherPosition
     *
     * Get the higher attribute position from a group attribute
     *
     * @param int $id_attribute_group
     * @return int $position
     */
    public static function getHigherPosition($id_attribute_group)
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'attribute`
				WHERE id_attribute_group = '.(int)$id_attribute_group;

        $position = DB::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }
}
