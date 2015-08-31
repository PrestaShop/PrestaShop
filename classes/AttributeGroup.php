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

class AttributeGroupCore extends ObjectModel
{
    /** @var string Name */
    public $name;
    public $is_color_group;
    public $position;
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
            'group_type' =>    array('type' => self::TYPE_STRING, 'required' => true),
            'position' =>        array('type' => self::TYPE_INT, 'validate' => 'isInt'),

            /* Lang fields */
            'name' =>            array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'public_name' =>    array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
        ),
    );


    protected $webserviceParameters = array(
        'objectsNodeName' => 'product_options',
        'objectNodeName' => 'product_option',
        'fields' => array(),
        'associations' => array(
            'product_option_values' => array(
                'resource' => 'product_option_value',
                'fields' => array(
                    'id' => array()
                ),
            ),
        ),
    );

    public function add($autodate = true, $nullValues = false)
    {
        $this->is_color_group = (int)($this->group_type == 'color');

        if ($this->position <= 0) {
            $this->position = AttributeGroup::getHigherPosition() + 1;
        }

        $return = parent::add($autodate, true);

        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));

        return $return;
    }

    public function update($nullValues = false)
    {
        $this->is_color_group = (int)($this->group_type == 'color');

        $return = parent::update($nullValues);

        Hook::exec('actionAttributeGroupSave', array('id_attribute_group' => $this->id));

        return $return;
    }

    public static function cleanDeadCombinations()
    {
        $attribute_combinations = Db::getInstance()->executeS('
			SELECT pac.`id_attribute`, pa.`id_product_attribute`
			FROM `'._DB_PREFIX_.'product_attribute` pa
			LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
				ON (pa.`id_product_attribute` = pac.`id_product_attribute`)');

        $return = true;

        foreach ($attribute_combinations as $attribute_combination) {
            if ((int)$attribute_combination['id_attribute'] == 0) {
                $id_product_attribute = (int)$attribute_combination['id_product_attribute'];
                $combination = new Combination($id_product_attribute);
                $return &= $combination->delete();
            }
        }

        return $return;
    }

    public function delete()
    {
        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {
            /* Select children in order to find linked combinations */
            $attribute_ids = Db::getInstance()->executeS('
				SELECT `id_attribute`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute_group` = '.(int)$this->id
            );

            if ($attribute_ids === false) {
                return false;
            }

            /* Removing attributes to the found combinations */
            $attributes_to_remove = array();
            foreach ($attribute_ids as $attribute) {
                $attributes_to_remove[] = (int)$attribute['id_attribute'];
            }

            if (!empty($attributes_to_remove)) {
                $items = implode(',', $attributes_to_remove);

                $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_attribute` IN ('.$items.')';
                if (!Db::getInstance()->execute($sql))
                    return false;

                /* Remove combinations if they do not possess attributes anymore */
                if (!AttributeGroup::cleanDeadCombinations()) {
                    return false;
                }

                /* Also delete related attributes */
                $sql = 'DELETE FROM `'._DB_PREFIX_.'attribute_lang` WHERE `id_attribute` IN ('.$items.')';
                if (!Db::getInstance()->execute($sql))
                    return false;

                $sql = 'DELETE FROM `'._DB_PREFIX_.'attribute_shop` WHERE `id_attribute` IN ('.$items.')';
                if (!Db::getInstance()->execute($sql))
                    return false;

                $sql = 'DELETE FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$this->id;
                if (!Db::getInstance()->execute($sql))
                    return false;
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
     * Get all attributes for a given language / group
     *
     * @param int $id_lang Language id
     * @param bool $id_attribute_group Attribute group id
     * @return array Attributes
     */
    public static function getAttributes($id_lang, $id_attribute_group)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $sql = 'SELECT *
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY `position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all attributes groups for a given language
     *
     * @param int $id_lang Language id
     * @return array Attributes groups
     */
    public static function getAttributesGroups($id_lang)
    {
        if (!Combination::isFeatureActive()) {
            return array();
        }

        $sql = 'SELECT DISTINCT agl.`name`, ag.*, agl.*
			FROM `'._DB_PREFIX_.'attribute_group` ag
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$id_lang.')
			ORDER BY `name` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Delete several objects from database
     *
     * return boolean Deletion result
     */
    public function deleteSelection($selection)
    {
        foreach ($selection as $id_selection) {
            $attribute_group = new AttributeGroup((int)$id_selection);

            if (!$attribute_group->delete()) {
                return false;
            }
        }

        return true;
    }

    public function setWsProductOptionValues($values)
    {
        $ids = array();

        foreach ($values as $value) {
            $ids[] = (int)$value['id'];
        }

        $items = implode(',', $ids);

        Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$this->id.'
			AND `id_attribute` NOT IN ('.$items.')'
        );

        return Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `id_attribute_group` = '.(int)$this->id.'
			WHERE `id_attribute`IN ('.$items.')'
        );
    }

    public function getWsProductOptionValues()
    {
        return Db::getInstance()->executeS('
			SELECT a.id_attribute AS id
			FROM `'._DB_PREFIX_.'attribute` a
			'.Shop::addSqlAssociation('attribute', 'a').'
			WHERE a.id_attribute_group = '.(int)$this->id
        );
    }

    /**
     * Move a group attribute
     * @param bool $way Up (1)  or Down (0)
     * @param int $position
     * @return bool Update result
     */
    public function updatePosition($way, $position)
    {
        $result = Db::getInstance()->executeS('
			SELECT ag.`position`, ag.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			WHERE ag.`id_attribute_group` = '.(int)Tools::getValue('id_attribute_group', 1).'
			ORDER BY ag.`position` ASC'
        );

        if (!$result) {
            return false;
        }

        foreach ($result as $attribute_group) {
            if ((int)$attribute_group['id_attribute_group'] == (int)$this->id) {
                $id_attribute_group = (int)$attribute_group['id_attribute_group'];
                $attribute_group_position = (int)$attribute_group['position'];
                break;
            }
        }

        if (!isset($id_attribute_group) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        $result = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position`= `position` '.($way ? '-' : '+').' 1
			WHERE `position` '.($way
                ? '> '.(int)$attribute_group_position.' AND `position` <= '.(int)$position
                : '< '.(int)$attribute_group_position.' AND `position` >= '.(int)$position));

        if (!$result) {
            return false;
        }

        return Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute_group`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute_group`='.(int)$id_attribute_group);
    }

    /**
     * Reorder group attribute position
     * Call it after deleting a group attribute.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        $results = Db::getInstance()->executeS('
			SELECT `id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group`
			ORDER BY `position`');

        if (empty($results)) {
            return true;
        }

        $position = 0;
        $return = true;

        foreach ($results as $value) {
            $return &= Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute_group`
				SET `position` = '.(int)$position++.'
				WHERE `id_attribute_group` = '.(int)$value['id_attribute_group']
            );
        }

        return $return;
    }

    /**
     * getHigherPosition
     *
     * Get the higher group attribute position
     *
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $position = DB::getInstance()->getValue('SELECT MAX(`position`) FROM `'._DB_PREFIX_.'attribute_group`');
        
        return (is_numeric($position)) ? $position : -1;
    }
}
