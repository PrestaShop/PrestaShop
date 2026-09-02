<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

/**
 * Class TranslatedConfigurationCore.
 */
class TranslatedConfigurationCore extends Configuration
{
    /** @var array */
    protected $webserviceParameters = [
        'objectNodeName' => 'translated_configuration',
        'objectsNodeName' => 'translated_configurations',
        'fields' => [
            'value' => [],
            'date_add' => [],
            'date_upd' => [],
        ],
    ];

    /** @var array */
    public static $definition = [
        'table' => 'configuration',
        'primary' => 'id_configuration',
        'multilang' => true,
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254],
            'id_shop_group' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_NOTHING, 'validate' => 'isUnsignedId'],
            'value' => ['type' => self::TYPE_STRING, 'lang' => true, 'size' => FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * TranslatedConfigurationCore constructor.
     *
     * @param int|null $id
     * @param int|null $idLang
     */
    public function __construct($id = null, $idLang = null)
    {
        $this->def = ObjectModel::getDefinition($this);
        // Check if the id configuration is set in the configuration_lang table.
        // Otherwise configuration is not set as translated configuration.
        if ($id !== null) {
            $idTranslated = Db::getInstance()->executeS('SELECT `' . bqSQL($this->def['primary']) . '`
				FROM `' . bqSQL(_DB_PREFIX_ . $this->def['table']) . '_lang`
				WHERE `' . bqSQL($this->def['primary']) . '`=' . (int) $id . ' LIMIT 0,1
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
        if (is_array($this->value)) {
            foreach ($this->value as $i18NValue) {
                if (Validate::isCleanHtml($i18NValue)) {
                    $ishtml = true;

                    break;
                }
            }
        }
        Configuration::updateValue($this->name, $this->value, $ishtml);

        $lastInsert = Db::getInstance()->getRow('
			SELECT `id_configuration` AS id
			FROM `' . _DB_PREFIX_ . 'configuration`
			WHERE `name` = \'' . pSQL($this->name) . '\'');
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
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public function getWebserviceObjectList($sqlJoin, $sqlFilter, $sqlSort, $sqlLimit)
    {
        $query = '
		SELECT DISTINCT main.`' . $this->def['primary'] . '` FROM `' . _DB_PREFIX_ . $this->def['table'] . '` main
		' . $sqlJoin . '
		WHERE id_configuration IN
		(	SELECT id_configuration
			FROM ' . _DB_PREFIX_ . $this->def['table'] . '_lang
		) ' . $sqlFilter . '
		' . ($sqlSort != '' ? $sqlSort : '') . '
		' . ($sqlLimit != '' ? $sqlLimit : '') . '
		';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }
}
