<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AttributeCore.
 */
class AttributeCore extends ObjectModel
{
    /** @var int Group id which attribute belongs */
    public $id_attribute_group;

    /** @var string Name */
    public $name;
    /** @var string $color */
    public $color;
    /** @var int $position */
    public $position;
    /** @todo Find type */
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
            'color' => array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
        ),
    );

    /** @var string $image_dir */
    protected $image_dir = _PS_COL_IMG_DIR_;

    /** @var array $webserviceParameters Web service parameters */
    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_option_values',
        'objectNodeName' => 'product_option_value',
        'fields' => array(
            'id_attribute_group' => array('xlink_resource' => 'product_options'),
        ),
    );

    /**
     * AttributeCore constructor.
     *
     * @param int|null $id Attribute ID
     * @param int|null $idLang Language ID
     * @param int|null $idShop Shop ID
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        $this->image_dir = _PS_COL_IMG_DIR_;
    }

    /**
     * @see ObjectModel::delete()
     */
    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            $result = Db::getInstance()->executeS('SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute_combination WHERE id_attribute = ' . (int) $this->id);
            $products = array();

            foreach ($result as $row) {
                $combination = new Combination($row['id_product_attribute']);
                $newRequest = Db::getInstance()->executeS('SELECT id_product, default_on FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . (int) $row['id_product_attribute']);
                foreach ($newRequest as $value) {
                    if ($value['default_on'] == 1) {
                        $products[] = $value['id_product'];
                    }
                }
                $combination->delete();
            }

            foreach ($products as $product) {
                $result = Db::getInstance()->executeS('SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = ' . (int) $product . ' LIMIT 1');
                foreach ($result as $row) {
                    if (Validate::isLoadedObject($product = new Product((int) $product))) {
                        $product->deleteDefaultAttributes();
                        $product->setDefaultAttribute($row['id_product_attribute']);
                    }
                }
            }

            // Delete associated restrictions on cart rules
            CartRule::cleanProductRuleIntegrity('attributes', $this->id);

            /* Reinitializing position */
            $this->cleanPositions((int) $this->id_attribute_group);
        }
        $return = parent::delete();
        if ($return) {
            Hook::exec('actionAttributeDelete', array('id_attribute' => $this->id));
        }

        return $return;
    }

    /**
     * @see ObjectModel::update()
     */
    public function update($nullValues = false)
    {
        $return = parent::update($nullValues);

        if ($return) {
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));
        }

        return $return;
    }

    /**
     * Adds current Attribute as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` column
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the Attribute has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = Attribute::getHigherPosition($this->id_attribute_group) + 1;
        }

        $return = parent::add($autoDate, $nullValues);

        if ($return) {
            Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));
        }

        return $return;
    }

    /**
     * Get all attributes for a given language.
     *
     * @param int $idLang Language ID
     * @param bool $notNull Get only not null fields if true
     *
     * @return array Attributes
     */
    public static function getAttributes($idLang, $notNull = false)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
			SELECT DISTINCT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $idLang . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $idLang . ')
			' . Shop::addSqlAssociation('attribute_group', 'ag') . '
			' . Shop::addSqlAssociation('attribute', 'a') . '
			' . ($notNull ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL AND agl.`id_attribute_group` IS NOT NULL' : '') . '
			ORDER BY agl.`name` ASC, a.`position` ASC
		');
    }

    /**
     * Check if the given name is an Attribute within the given AttributeGroup.
     *
     * @param int $idAttributeGroup AttributeGroup
     * @param string $name Attribute name
     * @param int $idLang Language ID
     *
     * @return array|bool
     */
    public static function isAttribute($idAttributeGroup, $name, $idLang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $result = Db::getInstance()->getValue('
			SELECT COUNT(*)
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int) $idLang . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $idLang . ')
			' . Shop::addSqlAssociation('attribute_group', 'ag') . '
			' . Shop::addSqlAssociation('attribute', 'a') . '
			WHERE al.`name` = \'' . pSQL($name) . '\' AND ag.`id_attribute_group` = ' . (int) $idAttributeGroup . '
			ORDER BY agl.`name` ASC, a.`position` ASC
		');

        return (int) $result > 0;
    }

    /**
     * Get quantity for a given attribute combination
     * Check if quantity is enough to serve the customer.
     *
     * @param int $idProductAttribute Product attribute combination id
     * @param int $qty Quantity needed
     * @param Shop $shop Shop
     *
     * @return bool Quantity is available or not
     */
    public static function checkAttributeQty($idProductAttribute, $qty, Shop $shop = null)
    {
        if (!$shop) {
            $shop = Context::getContext()->shop;
        }

        $result = StockAvailable::getQuantityAvailableByProduct(null, (int) $idProductAttribute, $shop->id);

        return $result && $qty <= $result;
    }

    /**
     * Return true if the Attribute is a color.
     *
     * @return bool Color is the attribute type
     */
    public function isColorAttribute()
    {
        if (!Db::getInstance()->getRow('
			SELECT `group_type`
			FROM `' . _DB_PREFIX_ . 'attribute_group`
			WHERE `id_attribute_group` = (
				SELECT `id_attribute_group`
				FROM `' . _DB_PREFIX_ . 'attribute`
				WHERE `id_attribute` = ' . (int) $this->id . ')
			AND group_type = \'color\'')) {
            return false;
        }

        return Db::getInstance()->numRows();
    }

    /**
     * Get minimal quantity for product with attributes quantity.
     *
     * @param int $idProductAttribute Product Attribute ID
     *
     * @return mixed Minimal quantity or false if no result
     */
    public static function getAttributeMinimalQty($idProductAttribute)
    {
        $minimalQuantity = Db::getInstance()->getValue(
            '
			SELECT `minimal_quantity`
			FROM `' . _DB_PREFIX_ . 'product_attribute_shop` pas
			WHERE `id_shop` = ' . (int) Context::getContext()->shop->id . '
			AND `id_product_attribute` = ' . (int) $idProductAttribute
        );

        if ($minimalQuantity > 1) {
            return (int) $minimalQuantity;
        }

        return false;
    }

    /**
     * Move an attribute inside its group.
     *
     * @param bool $direction Up (1) or Down (0)
     * @param int $position Current position of the attribute
     *
     * @return bool Update result
     */
    public function updatePosition($direction, $position)
    {
        if (!$idAttributeGroup = (int) Tools::getValue('id_attribute_group')) {
            $idAttributeGroup = (int) $this->id_attribute_group;
        }

        $sql = '
			SELECT a.`id_attribute`, a.`position`, a.`id_attribute_group`
			FROM `' . _DB_PREFIX_ . 'attribute` a
			WHERE a.`id_attribute_group` = ' . (int) $idAttributeGroup . '
			ORDER BY a.`position` ASC';

        if (!$res = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($res as $attribute) {
            if ((int) $attribute['id_attribute'] == (int) $this->id) {
                $movedAttribute = $attribute;
            }
        }

        if (!isset($movedAttribute) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases

        $res1 = Db::getInstance()->execute(
            '
			UPDATE `' . _DB_PREFIX_ . 'attribute`
			SET `position`= `position` ' . ($direction ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($direction
                ? '> ' . (int) $movedAttribute['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $movedAttribute['position'] . ' AND `position` >= ' . (int) $position) . '
			AND `id_attribute_group`=' . (int) $movedAttribute['id_attribute_group']
        );

        $res2 = Db::getInstance()->execute(
            '
			UPDATE `' . _DB_PREFIX_ . 'attribute`
			SET `position` = ' . (int) $position . '
			WHERE `id_attribute` = ' . (int) $movedAttribute['id_attribute'] . '
			AND `id_attribute_group`=' . (int) $movedAttribute['id_attribute_group']
        );

        return $res1 && $res2;
    }

    /**
     * Reorder the attribute position within the Attribute group.
     * Call this method after deleting an attribute from a group.
     *
     * @param int $idAttributeGroup Attribute group ID
     * @param bool $useLastAttribute
     *
     * @return bool Whether the result was successfully updated
     */
    public function cleanPositions($idAttributeGroup, $useLastAttribute = true)
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'attribute` SET `position` = @i:=@i+1 WHERE';

        if ($useLastAttribute) {
            $sql .= ' `id_attribute` != ' . (int) $this->id . ' AND';
        }

        $sql .= ' `id_attribute_group` = ' . (int) $idAttributeGroup . ' ORDER BY `position` ASC';

        return Db::getInstance()->execute($sql);
    }

    /**
     * get highest position.
     *
     * Get the highest attribute position from a group attribute
     *
     * @param int $idAttributeGroup AttributeGroup ID
     *
     * @return int $position Position
     * @todo: Shouldn't this be called getHighestPosition instead?
     */
    public static function getHigherPosition($idAttributeGroup)
    {
        $sql = 'SELECT MAX(`position`)
				FROM `' . _DB_PREFIX_ . 'attribute`
				WHERE id_attribute_group = ' . (int) $idAttributeGroup;

        $position = Db::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }
}
