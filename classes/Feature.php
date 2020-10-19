<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class FeatureCore.
 */
class FeatureCore extends ObjectModel
{
    /** @var array<string> Name */
    public $name;

    /** @var int */
    public $position;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'feature',
        'primary' => 'id_feature',
        'multilang' => true,
        'fields' => [
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],

            /* Lang fields */
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'product_features',
        'objectNodeName' => 'product_feature',
        'fields' => [],
    ];

    /**
     * Get a feature data for a given id_feature and id_lang.
     *
     * @param int $idLang Language ID
     * @param int $idFeature Feature ID
     *
     * @return array Array with feature's data
     */
    public static function getFeature($idLang, $idFeature)
    {
        return Db::getInstance()->getRow(
            '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'feature` f
			LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl
				ON ( f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = ' . (int) $idLang . ')
			WHERE f.`id_feature` = ' . (int) $idFeature
        );
    }

    /**
     * Get all features for a given language.
     *
     * @param int $idLang Language id
     *
     * @return array Multiple arrays with feature's data
     */
    public static function getFeatures($idLang, $withShop = true)
    {
        return Db::getInstance()->executeS('
		SELECT DISTINCT f.id_feature, f.*, fl.*
		FROM `' . _DB_PREFIX_ . 'feature` f
		' . ($withShop ? Shop::addSqlAssociation('feature', 'f') : '') . '
		LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` fl ON (f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = ' . (int) $idLang . ')
		ORDER BY f.`position` ASC');
    }

    /**
     * Delete several objects from database.
     *
     * @param array $selection Array with items to delete
     *
     * @return bool Deletion result
     */
    public function deleteSelection($selection)
    {
        /* Also delete Attributes */
        foreach ($selection as $value) {
            $obj = new Feature($value);
            if (!$obj->delete()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds current Feature as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Feature has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ($this->position <= 0) {
            $this->position = Feature::getHigherPosition() + 1;
        }

        $return = parent::add($autoDate, true);
        Hook::exec('actionFeatureSave', ['id_feature' => $this->id]);

        return $return;
    }

    /**
     * Updates the current Feature in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Feature has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        $this->clearCache();

        $result = 1;
        $fields = $this->getFieldsLang();
        foreach ($fields as $field) {
            foreach (array_keys($field) as $key) {
                if (!Validate::isTableOrIdentifier($key)) {
                    die(Tools::displayError());
                }
            }

            $sql = 'SELECT `id_lang` FROM `' . pSQL(_DB_PREFIX_ . $this->def['table']) . '_lang`
					WHERE `' . $this->def['primary'] . '` = ' . (int) $this->id . '
						AND `id_lang` = ' . (int) $field['id_lang'];
            $mode = Db::getInstance()->getRow($sql);
            $result &= (!$mode) ? Db::getInstance()->insert($this->def['table'] . '_lang', $field) :
                Db::getInstance()->update(
                    $this->def['table'] . '_lang',
                    $field,
                    '`' . $this->def['primary'] . '` = ' . (int) $this->id . ' AND `id_lang` = ' . (int) $field['id_lang']
                );
        }
        if ($result) {
            $result &= parent::update($nullValues);
            if ($result) {
                Hook::exec('actionFeatureSave', ['id_feature' => $this->id]);
            }
        }

        return $result;
    }

    /**
     * Deletes current Feature from the database.
     *
     * @return bool `true` if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        /* Also delete related attributes */
        Db::getInstance()->execute('
			DELETE
				`' . _DB_PREFIX_ . 'feature_value_lang`
			FROM
				`' . _DB_PREFIX_ . 'feature_value_lang`
				JOIN `' . _DB_PREFIX_ . 'feature_value`
					ON (`' . _DB_PREFIX_ . 'feature_value_lang`.id_feature_value = `' . _DB_PREFIX_ . 'feature_value`.id_feature_value)
			WHERE
				`' . _DB_PREFIX_ . 'feature_value`.`id_feature` = ' . (int) $this->id . '
		');
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'feature_value`
			WHERE `id_feature` = ' . (int) $this->id
        );
        // Also delete related products
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
			WHERE `id_feature` = ' . (int) $this->id
        );

        $return = parent::delete();
        if ($return) {
            Hook::exec('actionFeatureDelete', ['id_feature' => $this->id]);
        }

        /* Reinitializing position */
        $this->cleanPositions();

        return $return;
    }

    /**
     * Count number of features for a given language.
     *
     * @param int $idLang Language id
     *
     *@return int Number of feature
     */
    public static function nbFeatures($idLang)
    {
        return Db::getInstance()->getValue('
		SELECT COUNT(*) as nb
		FROM `' . _DB_PREFIX_ . 'feature` ag
		LEFT JOIN `' . _DB_PREFIX_ . 'feature_lang` agl
		ON (ag.`id_feature` = agl.`id_feature` AND `id_lang` = ' . (int) $idLang . ')
		');
    }

    /**
     * Create a feature from import.
     *
     * @param string $name Feature name
     * @param bool $position Feature position
     *
     * @return int Feature ID
     */
    public static function addFeatureImport($name, $position = false)
    {
        $rq = Db::getInstance()->getRow('
			SELECT `id_feature`
			FROM ' . _DB_PREFIX_ . 'feature_lang
			WHERE `name` = \'' . pSQL($name) . '\'
			GROUP BY `id_feature`
		');
        if (empty($rq)) {
            // Feature doesn't exist, create it
            $feature = new Feature();
            $feature->name = array_fill_keys(Language::getIDs(), (string) $name);
            if ($position) {
                $feature->position = (int) $position;
            } else {
                $feature->position = Feature::getHigherPosition() + 1;
            }
            $feature->add();

            return $feature->id;
        } elseif (isset($rq['id_feature']) && $rq['id_feature']) {
            if (is_numeric($position) && $feature = new Feature((int) $rq['id_feature'])) {
                $feature->position = (int) $position;
                if (Validate::isLoadedObject($feature)) {
                    $feature->update();
                }
            }

            return (int) $rq['id_feature'];
        }
    }

    /**
     * This metohd is allow to know if a feature is used or active.
     *
     * @return bool
     *
     * @since 1.5.0.1
     */
    public static function isFeatureActive()
    {
        return (bool) Configuration::get('PS_FEATURE_FEATURE_ACTIVE');
    }

    /**
     * Move a feature.
     *
     * @param bool $way Up (1)  or Down (0)
     * @param int $position
     *
     * @return bool Update result
     */
    public function updatePosition($way, $position, $idFeature = null)
    {
        if (!$res = Db::getInstance()->executeS(
            '
			SELECT `position`, `id_feature`
			FROM `' . _DB_PREFIX_ . 'feature`
			WHERE `id_feature` = ' . (int) ($idFeature ? $idFeature : $this->id) . '
			ORDER BY `position` ASC'
        )) {
            return false;
        }

        foreach ($res as $feature) {
            if ((int) $feature['id_feature'] == (int) $this->id) {
                $moved_feature = $feature;
            }
        }

        if (!isset($moved_feature) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'feature`
			SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
			WHERE `position`
			' . ($way
                ? '> ' . (int) $moved_feature['position'] . ' AND `position` <= ' . (int) $position
                : '< ' . (int) $moved_feature['position'] . ' AND `position` >= ' . (int) $position))
        && Db::getInstance()->execute('
			UPDATE `' . _DB_PREFIX_ . 'feature`
			SET `position` = ' . (int) $position . '
			WHERE `id_feature`=' . (int) $moved_feature['id_feature']);
    }

    /**
     * Reorder feature position
     * Call it after deleting a feature.
     *
     * @return bool $return
     */
    public static function cleanPositions()
    {
        Db::getInstance()->execute('SET @i = -1', false);
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'feature` SET `position` = @i:=@i+1 ORDER BY `position` ASC';

        return (bool) Db::getInstance()->execute($sql);
    }

    /**
     * getHigherPosition.
     *
     * Get the higher feature position
     *
     * @return int $position
     */
    public static function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `' . _DB_PREFIX_ . 'feature`';
        $position = Db::getInstance()->getValue($sql);

        return (is_numeric($position)) ? $position : -1;
    }
}
