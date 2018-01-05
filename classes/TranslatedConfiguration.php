<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class TranslatedConfigurationCore
 */
class TranslatedConfigurationCore extends Configuration
{
    protected $webserviceParameters = array(
        'objectNodeName' => 'translated_configuration',
        'objectsNodeName' => 'translated_configurations',
        'fields' => array(
            'value' => array(),
            'date_add' => array(),
            'date_upd' => array(),
        ),
    );

    public static $definition = array(
        'table' => 'configuration',
        'primary' => 'id_configuration',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 32),
            'id_shop_group' => array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
            'id_shop' => array('type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'),
            'value' => array('type' => self::TYPE_STRING, 'lang' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * TranslatedConfigurationCore constructor.
     *
     * @param null $id
     * @param null $idLang
     */
    public function __construct($id = null, $idLang = null)
    {
        $this->def = ObjectModel::getDefinition($this);
        // Check if the id configuration is set in the configuration_lang table.
        // Otherwise configuration is not set as translated configuration.
        if ($id !== null) {
            $idTranslated = Db::getInstance()->executeS('				SELECT `'.bqSQL($this->def['primary']).'`
				FROM `'.bqSQL(_DB_PREFIX_.$this->def['table']).'_lang`
				WHERE `'.bqSQL($this->def['primary']).'`='.(int)$id.' LIMIT 0,1
			');

            if (empty($idTranslated)) {
                $id = null;
            }
        }
        parent::__construct($id, $idLang);
    }

    /**
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     */
    public function add($autoDate = true, $nullValues = false)
    {
        return $this->update($nullValues);
    }

    /**
     * @param bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = false)
    {
        $ishtml = false;
        foreach ($this->value as $i18NValue) {
            if (Validate::isCleanHtml($i18NValue)) {
                $ishtml = true;
                break;
            }
        }
        Configuration::updateValue($this->name, $this->value, $ishtml);

        $lastInsert = Db::getInstance()->getRow('
			SELECT `id_configuration` AS id
			FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` = \''.pSQL($this->name).'\'');
        if ($lastInsert) {
            $this->id = $lastInsert['id'];
        }

        return true;
    }

    /**
     * @param string $sqlJoin
     * @param string $sqlFilter
     * @param string $sqlSort
     * @param string $sqlLimit
     *
     * @return array|false|mysqli_result|null|PDOStatement|resource
     */
    public function getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit)
    {
        $query = '
		SELECT DISTINCT main.`'.$this->def['primary'].'` FROM `'._DB_PREFIX_.$this->def['table'].'` main
		'.$sqlJoin.'
		WHERE id_configuration IN
		(	SELECT id_configuration
			FROM '._DB_PREFIX_.$this->def['table'].'_lang
		) '.$sqlFilter.'
		'.($sqlSort != '' ? $sqlSort : '').'
		'.($sqlLimit != '' ? $sqlLimit : '').'
		';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
