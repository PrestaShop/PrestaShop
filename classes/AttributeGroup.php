<?php
/**
 * 2007-2018 PrestaShop.
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

/**
 * Class AttributeGroupCore.
 */
class AttributeGroupCore extends ObjectModel
{
    /** @var string Name */
    public $name;
    /** @var bool $is_color_group Whether the attribute group is a color group */
    public $is_color_group;
    /** @var int $position Position */
    public $position;
    /** @var string $group_type Group type */
    public $group_type;

    /** @var string Public Name */
    public $public_name;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'attribute_group',
        'primary' => 'id_attribute_group',
        'multilang' => true,
        'fields' => array(
            'is_color_group' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'group_type' => array('type' => self::TYPE_STRING, 'required' => true),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'public_name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
    );

    /** @var array $webserviceParameters Web service parameters */
    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_options',
        'objectNodeName' => 'product_option',
        'fields' => array(),
        'associations' => array(
            'product_option_values' => array(
                'resource' => 'product_option_value',
                'fields' => array(
                    'id' => array(),
                ),
            ),
        ),
    );

    /**
     * Adds current AttributeGroup as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` column
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the AttributeGroup has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->group_type == 'color') {
            $this->is_color_group = 1;
        } else {
            $this->is_color_group = 0;
        }

        if ($this->position <= 0) {
            $this->position = AttributeGroup::getHigherPosition() + 1;
        }

        $return = parent::add($autoDate, true);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));

        return $return;
    }

    /**
     * Updates the current object in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Whether the AttributeGroup has been succesfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if ($this->group_type == 'color') {
            $this->is_color_group = 1;
        } else {
            $this->is_color_group = 0;
        }

        $return = parent::update($nullValues);
        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));

        return $return;
    }

    /**
     * Clean dead combinations
     * A combination is considered dead when its Attribute ID cannot be found.
     *
     * @return bool Whether the dead combinations have been successfully deleted
     */
    public static function cleanDeadCombinations()
    {
        $attributeCombinations = Db::getInstance()->executeS('
			SELECT pac.`id_attribute`, pa.`id_product_attribute`
			FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		');
        $toRemove = array();
        foreach ($attributeCombinations as $attributeCombination) {
            if ((int) $attributeCombination['id_attribute'] == 0) {
                $toRemove[] = (int) $attributeCombination['id_product_attribute'];
            }
        }
        $return = true;
        if (!empty($toRemove)) {
            foreach ($toRemove as $remove) {
                $combination = new Combination($remove);
                $return &= $combination->delete();
            }
        }

        return $return;
    }

    /**
     * Deletes current AttributeGroup from database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            /* Select children in order to find linked combinations */
            $attributeIds = Db::getInstance()->executeS('
				SELECT `id_attribute`
				FROM `' . _DB_PREFIX_ . 'attribute`
				WHERE `id_attribute_group` = ' . (int) $this->id
            );
            if ($attributeIds === false) {
                return false;
            }
            /* Removing attributes to the found combinations */
            $toRemove = array();
            foreach ($attributeIds as $attribute) {
                $toRemove[] = (int) $attribute['id_attribute'];
            }
            if (!empty($toRemove) && Db::getInstance()->execute('
				DELETE FROM `' . _DB_PREFIX_ . 'product_attribute_combination`
				WHERE `id_attribute`
					IN (' . implode(', ', $toRemove) . ')') === false) {
                return false;
            }
            /* Remove combinations if they do not possess attributes anymore */
            if (!AttributeGroup::cleanDeadCombinations()) {
                return false;
            }
            /* Also delete related attributes */
            if (count($toRemove)) {
                if (!Db::getInstance()->execute('
				DELETE FROM `' . _DB_PREFIX_ . 'attribute_lang`
				WHERE `id_attribute`	IN (' . implode(',', $toRemove) . ')') ||
                !Db::getInstance()->execute('
				DELETE FROM `' . _DB_PREFIX_ . 'attribute_shop`
				WHERE `id_attribute`	IN (' . implode(',', $toRemove) . ')') ||
                !Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'attribute` WHERE `id_attribute_group` = ' . (int) $this->id)) {
                    return false;
                }
            }
            $this->cleanPositions();
        }
        $return = parent::delete();
        if ($return) {
            Hook::exec('actionAttributeGroupDelete', array('id_attribute_group' => $this->id));
        }

        return $return;
    }

    /**
     * Get all attributes for a given language / group.
     *
     * @param int $idLang Language ID
     * @param bool $idAttributeGroup AttributeGroup ID
     *
     * @return array Attributes
     */
    public static function getAttributes($idLang, $idAttributeGroup)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'attribute` a
			' . Shop::addSqlAssociation('attribute', 'a') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) $idLang . ')
			WHERE a.`id_attribute_group` = ' . (int) $idAttributeGroup . '
			ORDER BY `position` ASC
		');
    }

    /**
     * Get all attributes groups for a given language.
     *
     * @param int $idLang Language id
     *
     * @return array Attributes groups
     */
    public static function getAttributesGroups($idLang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        return Db::getInstance()->executeS('
			SELECT DISTINCT agl.`name`, ag.*, agl.*
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			' . Shop::addSqlAssociation('attribute_group', 'ag') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = ' . (int) $idLang . ')
			ORDER BY `name` ASC
		');
    }

    /**
     * Delete several objects from database.
     *
     * @param array $selection Array with AttributeGroup IDs
     *
     * @return bool Deletion result
     */
    public function deleteSelection($selection)
    {
        /* Also delete Attributes */
        foreach ($selection as $value) {
            $obj = new AttributeGroup($value);
            if (!$obj->delete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set the values of the current AttributeGroup for the webservice.
     *
     * @param array $values
     *
     * @return bool Whether the update was successful
     */
    public function setWsProductOptionValues($values)
    {
        $ids = array();
        foreach ($values as $value) {
            $ids[] = intval($value['id']);
        }
        Db::getInstance()->execute('
			DELETE FROM `' . _DB_PREFIX_ . 'attribute`
			WHERE `id_attribute_group` = ' . (int) $this->id . '
			AND `id_attribute` NOT IN (' . implode(',', $ids) . ')'
        );
        $ok = true;
        foreach ($values as $value) {
            $result = Db::getInstance()->execute('
				UPDATE `' . _DB_PREFIX_ . 'attribute`
				SET `id_attribute_group` = ' . (int) $this->id . '
				WHERE `id_attribute` = ' . (int) $value['id']
            );
            if ($result === false) {
                $ok = false;
            }
        }

        return $ok;
    }

    /**
     * Get values of current AttributeGroup instance for the webservice.
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWsProductOptionValues()
    {
        $result = Db::getInstance()->executeS('
			SELECT a.id_attribute AS id
			FROM `' . _DB_PREFIX_ . 'attribute` a
			' . Shop::addSqlAssociation('attribute', 'a') . '
			WHERE a.id_attribute_group = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * Move a group attribute.
     *
     * @param bool $direction Up (1) or Down (0)
     * @param int $position
     *
     * @return bool Update result
     */
    public function updatePosition($direction, $position)
    {
        if (!$res = Db::getInstance()->executeS('
			SELECT ag.`position`, ag.`id_attribute_group`
			FROM `' . _DB_PREFIX_ . 'attribute_group` ag
			WHERE ag.`id_attribute_group` = ' . (int) Tools::getValue('id_attribute_group', 1) . '
			ORDER BY ag.`position` ASC'
        )) {
            return false;
        }

        foreach ($res as $groupAttribute) {
            if ((int) $groupAttribute['id_attribute_group'] == (int) $this->id) {
                $movedGroupAttribute = $groupAttribute;
            }
        }

        if (!isset($movedGroupAttribute) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'attribute_group`
			SET `position`= `position` ' . ($direction ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($direction
                ? '> ' . (int) $movedGroupAttribute['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $movedGroupAttribute['position'] . ' AND `position` >= ' . (int) $position)
        ) && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'attribute_group`
			SET `position` = ' . (int) $position . '
			WHERE `id_attribute_group`=' . (int) $movedGroupAttribute['id_attribute_group'])
        ;
    }

    /**
     * Reorder group attribute position
     * Call it after deleting a group attribute.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $return = true;
        Db::getInstance()->execute('SET @i = -1', false);
        $return = Db::getInstance()->execute('
				UPDATE `' . _DB_PREFIX_ . 'attribute_group`
				SET `position` = @i:=@i+1
				ORDER BY `position`'
        );

        return $return;
    }

    /**
     * Get the highest AttributeGroup position.
     *
     * @return int $position Position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `' . _DB_PREFIX_ . 'attribute_group`';
        $position = Db::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }
}
