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
 * Class FeatureValueCore.
 */
class FeatureValueCore extends ObjectModel
{
    /** @var int Group id which attribute belongs */
    public $id_feature;

    /** @var string|array Name */
    public $value;

    /** @var bool Custom */
    public $custom = 0;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'feature_value',
        'primary' => 'id_feature_value',
        'multilang' => true,
        'fields' => [
            'id_feature' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'custom' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],

            /* Lang fields */
            'value' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
        ],
    ];

    protected $webserviceParameters = [
        'objectsNodeName' => 'product_feature_values',
        'objectNodeName' => 'product_feature_value',
        'fields' => [
            'id_feature' => ['xlink_resource' => 'product_features'],
        ],
    ];

    /**
     * Get all values for a given feature.
     *
     * @param int $idFeature Feature id
     *
     * @return array Array with feature's values
     */
    public static function getFeatureValues($idFeature)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT *
			FROM `' . _DB_PREFIX_ . 'feature_value`
			WHERE `id_feature` = ' . (int) $idFeature
        );
    }

    /**
     * Get all values for a given feature and language.
     *
     * @param int $idLang Language id
     * @param int $idFeature Feature id
     *
     * @return array Array with feature's values
     */
    public static function getFeatureValuesWithLang($idLang, $idFeature, $custom = false)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'feature_value` v
			LEFT JOIN `' . _DB_PREFIX_ . 'feature_value_lang` vl
				ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = ' . (int) $idLang . ')
			WHERE v.`id_feature` = ' . (int) $idFeature . '
				' . (!$custom ? 'AND (v.`custom` IS NULL OR v.`custom` = 0)' : '') . '
			ORDER BY vl.`value` ASC
		');
    }

    /**
     * Get all language for a given value.
     *
     * @param int $idFeatureValue Feature value id
     *
     * @return array Array with value's languages
     */
    public static function getFeatureValueLang($idFeatureValue)
    {
        return Db::getInstance()->executeS('
			SELECT *
			FROM `' . _DB_PREFIX_ . 'feature_value_lang`
			WHERE `id_feature_value` = ' . (int) $idFeatureValue . '
			ORDER BY `id_lang`
		');
    }

    /**
     * Select the good lang in tab.
     *
     * @param array $lang Array with all language
     * @param int $idLang Language id
     *
     * @return string String value name selected
     */
    public static function selectLang($lang, $idLang)
    {
        foreach ($lang as $tab) {
            if ($tab['id_lang'] == $idLang) {
                return $tab['value'];
            }
        }
    }

    /**
     * Add FeatureValue from import.
     *
     * @param int $idFeature
     * @param string $value
     * @param null $idProduct
     * @param null $idLang
     * @param bool $custom
     *
     * @return int
     */
    public static function addFeatureValueImport($idFeature, $value, $idProduct = null, $idLang = null, $custom = false)
    {
        $idFeatureValue = false;
        if (null !== $idProduct && $idProduct) {
            $idFeatureValue = Db::getInstance()->getValue('
				SELECT fp.`id_feature_value`
				FROM ' . _DB_PREFIX_ . 'feature_product fp
				INNER JOIN ' . _DB_PREFIX_ . 'feature_value fv USING (`id_feature_value`)
				WHERE fp.`id_feature` = ' . (int) $idFeature . '
				AND fv.`custom` = ' . (int) $custom . '
				AND fp.`id_product` = ' . (int) $idProduct);

            if ($custom && $idFeatureValue && null !== $idLang && $idLang) {
                Db::getInstance()->execute('
				UPDATE ' . _DB_PREFIX_ . 'feature_value_lang
				SET `value` = \'' . pSQL($value) . '\'
				WHERE `id_feature_value` = ' . (int) $idFeatureValue . '
				AND `value` != \'' . pSQL($value) . '\'
				AND `id_lang` = ' . (int) $idLang);
            }
        }

        if (!$custom) {
            $idFeatureValue = Db::getInstance()->getValue('
				SELECT fv.`id_feature_value`
				FROM ' . _DB_PREFIX_ . 'feature_value fv
				LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.`id_feature_value` = fv.`id_feature_value` AND fvl.`id_lang` = ' . (int) $idLang . ')
				WHERE `value` = \'' . pSQL($value) . '\'
				AND fv.`id_feature` = ' . (int) $idFeature . '
				AND fv.`custom` = 0
				GROUP BY fv.`id_feature_value`');
        }

        if ($idFeatureValue) {
            return (int) $idFeatureValue;
        }

        // Feature doesn't exist, create it
        $feature_value = new FeatureValue();
        $feature_value->id_feature = (int) $idFeature;
        $feature_value->custom = (bool) $custom;
        $feature_value->value = array_fill_keys(Language::getIDs(false), $value);
        $feature_value->add();

        return (int) $feature_value->id;
    }

    /**
     * Adds current FeatureValue as a new Object to the database.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the FeatureValue has been successfully added
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $return = parent::add($autoDate, $nullValues);
        if ($return) {
            Hook::exec('actionFeatureValueSave', ['id_feature_value' => $this->id]);
        }

        return $return;
    }

    /**
     * Updates the current FeatureValue in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the FeatureValue has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        $return = parent::update($nullValues);
        if ($return) {
            Hook::exec('actionFeatureValueSave', ['id_feature_value' => $this->id]);
        }

        return $return;
    }

    /**
     * Deletes current FeatureValue from the database.
     *
     * @return bool `true` if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        /* Also delete related products */
        Db::getInstance()->execute(
            '
			DELETE FROM `' . _DB_PREFIX_ . 'feature_product`
			WHERE `id_feature_value` = ' . (int) $this->id
        );
        $return = parent::delete();

        if ($return) {
            Hook::exec('actionFeatureValueDelete', ['id_feature_value' => $this->id]);
        }

        return $return;
    }
}
