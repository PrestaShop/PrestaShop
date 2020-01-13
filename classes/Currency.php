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
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

class CurrencyCore extends ObjectModel
{
    public $id;

    /**
     * Name of the currency.
     *
     * @var string
     */
    public $name;

    /**
     * Localized names of the currency
     *
     * @var string[]
     */
    protected $localizedNames;

    /**
     * Alphabetic ISO 4217 code of this currency.
     *
     * @var string
     */
    public $iso_code;

    /**
     * Numeric ISO 4217 code of this currency
     *
     * @var string
     */
    public $iso_code_num;

    /**
     * Numeric ISO 4217 code of this currency.
     *
     * @var string
     */
    public $numeric_iso_code;

    /**
     * Exchange rate from default currency.
     *
     * @var float
     */
    public $conversion_rate;

    /**
     * Is this currency deleted ?
     * If currency is deleted, it stays in database. This is just a state (soft delete).
     *
     * @var bool
     */
    public $deleted = 0;

    /**
     * Is this currency active ?
     *
     * @var int|bool active
     */
    public $active;

    /**
     * Currency's symbol
     *
     * @var string
     */
    public $sign;

    /**
     * Currency's symbol.
     *
     * @var string
     */
    public $symbol;

    /**
     * Localized Currency's symbol.
     *
     * @var string[]
     */
    private $localizedSymbols;

    /**
     * CLDR price formatting pattern
     * e.g.: In french (fr-FR), price formatting pattern is : #,##0.00 ¤.
     *
     * @var string
     */
    public $format;

    /**
     * @var int
     */
    public $blank;

    /**
     * Use decimals when displaying a price in this currency
     *
     * @deprecated since 1.7.0
     *
     * @var int
     */
    public $decimals;

    /**
     * Number of decimal digits to use when displaying a price in this currency.
     *
     * @var int
     */
    public $precision;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'currency',
        'primary' => 'id_currency',
        'multilang' => true,
        'fields' => array(
            'iso_code' => array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 3),
            'numeric_iso_code' => array('type' => self::TYPE_STRING, 'validate' => 'isNumericIsoCode', 'size' => 3),
            'precision' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'conversion_rate' => array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true, 'shop' => true),
            'deleted' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

            /* Lang fields */
            'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
            'symbol' => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 255),
        ),
    );

    /** @var array Currency cache */
    protected static $currencies = array();
    protected static $countActiveCurrencies = array();

    protected $webserviceParameters = array(
        'objectsNodeName' => 'currencies',
        'fields' => array(
            'name' => array(
                'setter' => false,
                'getter' => 'getName',
                'modifier' => array(
                    'http_method' => WebserviceRequest::HTTP_POST | WebserviceRequest::HTTP_PUT,
                    'modifier' => 'setNameForWebservice',
                ),
            ),
            'symbol' => array(
                'setter' => false,
                'getter' => 'getSymbol',
                'modifier' => array(
                    'http_method' => WebserviceRequest::HTTP_POST | WebserviceRequest::HTTP_PUT,
                    'modifier' => 'setSymbolForWebservice',
                ),
            ),
        ),
    );

    /**
     * contains the sign to display before price, according to its format.
     *
     * @var string
     */
    public $prefix = null;

    /**
     * contains the sign to display after price, according to its format.
     *
     * @var string
     */
    public $suffix = null;

    /**
     * CurrencyCore constructor.
     *
     * @param null $id
     * @param false|null $idLang if null or false, default language will be used
     * @param null $idShop
     */
    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);

        if ($this->iso_code) {
            // As the CLDR used to return a string even if in multi shop / lang,
            // We force only one string to be returned
            if (empty($idLang)) {
                $idLang = Context::getContext()->language->id;
            }
            if (is_array($this->symbol)) {
                $this->localizedSymbols = $this->symbol;
                $this->sign = $this->symbol = $this->symbol[$idLang];
            } else {
                $this->localizedSymbols = [$idLang => $this->symbol];
                $this->sign = $this->symbol;
            }

            if (is_array($this->name)) {
                $this->localizedNames = $this->name;
                $this->name = Tools::ucfirst($this->name[$idLang]);
            } else {
                $this->localizedNames = [$idLang => $this->name];
                $this->name = Tools::ucfirst($this->name);
            }

            $this->iso_code_num = $this->numeric_iso_code;

            $this->blank = 1;
            $this->decimals = 1;
        }

        if (!$this->conversion_rate) {
            $this->conversion_rate = 1;
        }
    }

    public function getWebserviceParameters($ws_params_attribute_name = null)
    {
        $parameters = parent::getWebserviceParameters($ws_params_attribute_name);
        // name & symbol are i18n fields but casted to single string in the constructor
        // so we need to force the webservice to consider those fields as non-i18n fields.
        // Also, in 1.7.5 the field symbol didn't exists and name wasn't an i18n field so in order
        // to keep 1.7.6 backward compatible we need to make those fields non-i18n.
        $parameters['fields']['name']['i18n'] = false;
        $parameters['fields']['symbol']['i18n'] = false;

        return $parameters;
    }

    public function setNameForWebservice()
    {
        $this->name = $this->localizedNames;
    }

    public function setSymbolForWebservice()
    {
        $this->symbol = $this->localizedSymbols;
    }

    /**
     * reset static cache (eg unit testing purpose).
     */
    public static function resetStaticCache()
    {
        static::$currencies = array();
        static::$countActiveCurrencies = array();
    }

    /**
     * Overriding check if currency rate is not empty and if currency with the same iso code already exists.
     * If it's true, currency is not added.
     *
     * @param bool $autoDate Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Currency has been successfully added
     */
    public function add($autoDate = true, $nullValues = false)
    {
        if ((float) $this->conversion_rate <= 0) {
            return false;
        }

        return Currency::exists($this->iso_code) ? false : parent::add($autoDate, $nullValues);
    }

    /**
     * Updates the current object in the database.
     *
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the CartRule has been successfully updated
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function update($nullValues = false)
    {
        if ((float) $this->conversion_rate <= 0) {
            return false;
        }

        return parent::update($nullValues);
    }

    /**
     * Check if a Currency already exists.
     *
     * @param int|string $isoCode int for iso code number string for iso code
     * @param int $idShop Shop ID
     *
     * @return bool Indicates whether the Currency already exists
     */
    public static function exists($isoCode, $idShop = 0)
    {
        $idCurrencyExists = Currency::getIdByIsoCode($isoCode, (int) $idShop);

        return (bool) $idCurrencyExists;
    }

    /**
     * Delete given Currencies.
     *
     * @param array $selection Currencies
     *
     * @return bool Indicates whether the selected Currencies have been succesfully deleted
     */
    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            return false;
        }

        $res = array();
        foreach ($selection as $id) {
            $obj = new Currency((int) $id);
            $res[$id] = $obj->delete();
        }

        foreach ($res as $value) {
            if (!$value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes current object from database.
     *
     * @return bool True if delete was successful
     *
     * @throws PrestaShopException
     */
    public function delete()
    {
        if ($this->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $result = Db::getInstance()->getRow('SELECT `id_currency` FROM ' . _DB_PREFIX_ . 'currency WHERE `id_currency` != ' . (int) $this->id . ' AND `deleted` = 0');
            if (!$result['id_currency']) {
                return false;
            }
            Configuration::updateValue('PS_CURRENCY_DEFAULT', $result['id_currency']);
        }
        $this->deleted = 1;

        // Remove currency restrictions
        $res = Db::getInstance()->delete('module_currency', 'id_currency = ' . (int) $this->id);

        return $res && $this->update();
    }

    /**
     * Return formatted sign.
     *
     * @param string $side left or right
     *
     * @return string formated sign
     */
    public function getSign($side = null)
    {
        return $this->sign;
    }

    /**
     * Is this currency installed for a given shop ?
     * (current shop by default).
     *
     * @param int|null $currencyId
     *                             The currency to look for (
     * @param int|null $shopId
     *                         The given shop's id
     *
     * @return bool
     *              True if this currency is installed for the given shop
     */
    public function isInstalled($currencyId = null, $shopId = null)
    {
        $currencyId = $currencyId ?: $this->id;
        $shopId = $shopId ?: Context::getContext()->shop->id;
        $sql = (new DbQuery())
            ->select('1')
            ->from('currency', 'c')
            ->innerJoin('currency_shop', 'cs', 'c.`id_currency` = cs.`id_currency`')
            ->where('c.`id_currency` = ' . (int) $currencyId)
            ->where('cs.`id_shop` = ' . (int) $shopId)
            ->where('c.`deleted` = 0')
            ->where('c.`active` = 1');

        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * Returns the name of the currency (using the translated name base on the id_lang
     * provided on creation). This method is useful when $this->name contains an array
     * but you still need to get its name as a string.
     *
     * @return string
     */
    public function getName()
    {
        if (is_string($this->name)) {
            return $this->name;
        }

        $id_lang = $this->id_lang;
        if ($id_lang === null) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Tools::ucfirst($this->name[$id_lang]);
    }

    public function getSymbol()
    {
        if (is_string($this->symbol)) {
            return $this->symbol;
        }

        $id_lang = $this->id_lang;
        if (null === $id_lang) {
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return Tools::ucfirst($this->symbol[$id_lang]);
    }

    /**
     * Return available currencies.
     *
     * @param bool $object
     * @param bool $active
     * @param bool $groupBy
     *
     * @return array Currencies
     */
    public static function getCurrencies($object = false, $active = true, $groupBy = false)
    {
        return static::addCldrDatasToCurrency(
            static::findAll($active, $groupBy),
            $object
        );
    }

    /**
     * Retrieve all currencies data from the database.
     *
     * @param bool $active If true only active are returned
     * @param bool $groupBy Group by id_currency
     * @param bool $currentShopOnly If true returns only currencies associated to current shop
     *
     * @return array Currency data from database
     *
     * @throws PrestaShopDatabaseException
     */
    public static function findAll($active = true, $groupBy = false, $currentShopOnly = true)
    {
        $currencies = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'currency` c
            ' . ($currentShopOnly ? Shop::addSqlAssociation('currency', 'c') : '') . '
                WHERE c.`deleted` = 0' .
                ($active ? ' AND c.`active` = 1' : '') .
                ($groupBy ? ' GROUP BY c.`id_currency`' : '') .
                ' ORDER BY `iso_code` ASC');

        return $currencies;
    }

    /**
     * Retrieve all currencies data from the database.
     *
     * @return array Currency data from database
     *
     * @throws PrestaShopDatabaseException
     */
    public static function findAllInstalled()
    {
        $currencies = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'currency` c ORDER BY `iso_code` ASC'
        );

        return $currencies;
    }

    public function getInstalledCurrencies($shopId = null)
    {
        $shopId = $shopId ?: Context::getContext()->shop->id;
        $sql = (new DbQuery())
            ->select('c.*, cl.*')
            ->from('currency', 'c')
            ->innerJoin('currency_lang', 'cl', 'c.`id_currency` = cl.`id_currency`')
            ->innerJoin('currency_shop', 'cs', 'c.`id_currency` = cs.`id_currency`')
            ->where('cs.`id_shop` = ' . (int) $shopId)
            ->where('c.`deleted` = 0')
            ->where('c.`active` = 1');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get Currencies by Shop ID.
     *
     * @param int $idShop Shop ID
     *
     * @return array|Currency
     */
    public static function getCurrenciesByIdShop($idShop = 0)
    {
        $currencies = Db::getInstance()->executeS('
		SELECT *
		FROM `' . _DB_PREFIX_ . 'currency` c
		LEFT JOIN `' . _DB_PREFIX_ . 'currency_shop` cs ON (cs.`id_currency` = c.`id_currency`)
        ' . ($idShop ? ' WHERE cs.`id_shop` = ' . (int) $idShop : '') . '
		ORDER BY `iso_code` ASC');

        return self::addCldrDatasToCurrency($currencies);
    }

    /**
     * Add Cldr datas to result query or signe object/array.
     *
     * @param $currencies mixed object|array
     * @param $isObject bool
     */
    protected static function addCldrDatasToCurrency($currencies, $isObject = false)
    {
        if (is_array($currencies)) {
            foreach ($currencies as $k => $c) {
                $currencies[$k] = Currency::getCurrencyInstance($c['id_currency']);
                if (!$isObject) {
                    $currencies[$k] = (array) $currencies[$k];
                    $currencies[$k]['id_currency'] = $currencies[$k]['id'];
                }
            }
        } else {
            $currencies = Currency::getCurrencyInstance($currencies['id_currency']);
            if (!$isObject) {
                $currencies = (array) $currencies;
                $currencies['id_currency'] = $currencies['id'];
            }
        }

        return $currencies;
    }

    public static function getPaymentCurrenciesSpecial($idModule, $idShop = null)
    {
        if (null === $idShop) {
            $idShop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT *
				FROM ' . _DB_PREFIX_ . 'module_currency
				WHERE id_module = ' . (int) $idModule . '
					AND id_shop =' . (int) $idShop;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Get payment Currencies.
     *
     * @param int $idModule Module ID
     * @param null $idShop Shop ID
     *
     * @return array|false|mysqli_result|PDOStatement|resource|null
     */
    public static function getPaymentCurrencies($idModule, $idShop = null)
    {
        if (null === $idShop) {
            $idShop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT c.*
				FROM `' . _DB_PREFIX_ . 'module_currency` mc
				LEFT JOIN `' . _DB_PREFIX_ . 'currency` c ON c.`id_currency` = mc.`id_currency`
				WHERE c.`deleted` = 0
					AND mc.`id_module` = ' . (int) $idModule . '
					AND c.`active` = 1
					AND mc.id_shop = ' . (int) $idShop . '
				ORDER BY c.`iso_code` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Check payment Currencies.
     *
     * @param int $idModule Module ID
     * @param null $idShop Shop ID
     *
     * @return array|PDOStatement|resource|null
     */
    public static function checkPaymentCurrencies($idModule, $idShop = null)
    {
        if (empty($idModule)) {
            return array();
        }

        if (null === $idShop) {
            $idShop = Context::getContext()->shop->id;
        }

        $sql = new DbQuery();
        $sql->select('mc.*');
        $sql->from('module_currency', 'mc');
        $sql->where('mc.`id_module` = ' . (int) $idModule);
        $sql->where('mc.`id_shop` = ' . (int) $idShop);

        $currencies = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        return $currencies ? $currencies : array();
    }

    /**
     * Get Currency.
     *
     * @param int $idCurrency Currency ID
     *
     * @return array|bool|object|null
     */
    public static function getCurrency($idCurrency)
    {
        $sql = new DbQuery();
        $sql->select('c.*');
        $sql->from('currency', 'c');
        $sql->where('`deleted` = 0');
        $sql->where('`id_currency` = ' . (int) $idCurrency);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    /**
     * Get Currency ID by ISO code.
     *
     * @param string $isoCode ISO code
     * @param int $idShop Shop ID
     * @param bool $forceRefreshCache [default=false] Set to TRUE to forcefully refresh any currently cached results
     *
     * @return int Currency ID
     */
    public static function getIdByIsoCode($isoCode, $idShop = 0, $forceRefreshCache = false)
    {
        $cacheId = 'Currency::getIdByIsoCode_' . pSQL($isoCode) . '-' . (int) $idShop;
        if ($forceRefreshCache || !Cache::isStored($cacheId)) {
            $query = Currency::getIdByQuery($idShop);
            $query->where('iso_code = \'' . pSQL($isoCode) . '\'');

            $result = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get Currency ID query.
     *
     * @param int $idShop Shop ID
     *
     * @return DbQuery
     */
    public static function getIdByQuery($idShop = 0)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('deleted = 0');

        if (Shop::isFeatureActive() && $idShop > 0) {
            $query->leftJoin('currency_shop', 'cs', 'cs.id_currency = c.id_currency');
            $query->where('id_shop = ' . (int) $idShop);
        }

        return $query;
    }

    /**
     * Refresh the currency exchange rate
     * The XML file define exchange rate for each from a default currency ($isoCodeSource).
     *
     * @param SimpleXMLElement $data XML content which contains all the exchange rates
     * @param string $isoCodeSource The default currency used in the XML file
     * @param Currency $defaultCurrency The default currency object
     */
    public function refreshCurrency($data, $isoCodeSource, $defaultCurrency)
    {
        // fetch the exchange rate of the default currency
        $exchangeRate = 1;
        $tmp = $this->conversion_rate;
        if ($defaultCurrency->iso_code != $isoCodeSource) {
            foreach ($data->currency as $currency) {
                if ($currency['iso_code'] == $defaultCurrency->iso_code) {
                    $exchangeRate = round((float) $currency['rate'], 6);

                    break;
                }
            }
        }

        if ($defaultCurrency->iso_code == $this->iso_code) {
            $this->conversion_rate = 1;
        } else {
            if ($this->iso_code == $isoCodeSource) {
                $rate = 1;
            } else {
                foreach ($data->currency as $obj) {
                    if ($this->iso_code == (string) ($obj['iso_code'])) {
                        $rate = (float) $obj['rate'];

                        break;
                    }
                }
            }

            if (isset($rate)) {
                $this->conversion_rate = round($rate / $exchangeRate, 6);
            }
        }

        if ($tmp != $this->conversion_rate) {
            $this->update();
        }
    }

    /**
     * Get default Currency.
     *
     * @return bool|Currency
     */
    public static function getDefaultCurrency()
    {
        $idCurrency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        if ($idCurrency == 0) {
            return false;
        }

        return new Currency($idCurrency);
    }

    /**
     * Refresh Currencies.
     *
     * @return string Error message
     */
    public static function refreshCurrencies()
    {
        // Parse
        if (!$feed = Tools::simplexml_load_file(_PS_CURRENCY_FEED_URL_)) {
            return Context::getContext()->getTranslator()->trans('Cannot parse feed.', array(), 'Admin.Notifications.Error');
        }

        // Default feed currency (EUR)
        $isoCodeSource = (string) ($feed->source['iso_code']);

        if (!$defaultCurrency = Currency::getDefaultCurrency()) {
            return Context::getContext()->getTranslator()->trans('No default currency', array(), 'Admin.Notifications.Error');
        }

        $currencies = Currency::getCurrencies(true, false, true);
        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            if ($currency->id != $defaultCurrency->id) {
                $currency->refreshCurrency($feed->list, $isoCodeSource, $defaultCurrency);
            }
        }

        return '';
    }

    /**
     * Get Currency instance.
     *
     * @param int $id Currency ID
     *
     * @return Currency
     */
    public static function getCurrencyInstance($id)
    {
        if (!isset(self::$currencies[$id])) {
            self::$currencies[(int) ($id)] = new Currency($id);
        }

        return self::$currencies[(int) ($id)];
    }

    /**
     * Get conversion rate.
     *
     * @return int|string
     *
     * @deprecated 1.7.2.0, use Currency::getConversionRate() instead
     */
    public function getConversationRate()
    {
        Tools::displayAsDeprecated('Use Currency::getConversionRate() instead');

        return $this->getConversionRate();
    }

    /**
     * Get conversion rate.
     *
     * @return int|string
     */
    public function getConversionRate()
    {
        return ($this->id != (int) Configuration::get('PS_CURRENCY_DEFAULT')) ? $this->conversion_rate : 1;
    }

    /**
     * Count active Currencies.
     *
     * @param int|null $idShop Shop ID
     *
     * @return mixed Amount of active Currencies
     *               `false` if none found
     */
    public static function countActiveCurrencies($idShop = null)
    {
        if ($idShop === null) {
            $idShop = (int) Context::getContext()->shop->id;
        }

        if (!isset(self::$countActiveCurrencies[$idShop])) {
            self::$countActiveCurrencies[$idShop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT c.id_currency) FROM `' . _DB_PREFIX_ . 'currency` c
				LEFT JOIN ' . _DB_PREFIX_ . 'currency_shop cs ON (cs.id_currency = c.id_currency AND cs.id_shop = ' . (int) $idShop . ')
				WHERE c.`active` = 1
			');
        }

        return self::$countActiveCurrencies[$idShop];
    }

    /**
     * Is multi Currency activated?
     *
     * @param int|null $idShop Shop ID
     *
     * @return bool Indicates whether multi Currency is actived
     */
    public static function isMultiCurrencyActivated($idShop = null)
    {
        return Currency::countActiveCurrencies($idShop) > 1;
    }

    /**
     * This method aims to update localized data in currency from CLDR reference.
     *
     * @param array $languages
     * @param LocaleRepository $localeRepoCLDR
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function refreshLocalizedCurrencyData(array $languages, LocaleRepository $localeRepoCLDR)
    {
        $symbolsByLang = $namesByLang = [];
        foreach ($languages as $languageData) {
            $language = new Language($languageData['id_lang']);
            if (empty($language->locale)) {
                // Language doesn't have locale we can't install this language
                continue;
            }

            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $localeRepoCLDR->getLocale($language->locale);
            // CLDR currency gives data from CLDR reference, for the given language
            $cldrCurrency = $cldrLocale->getCurrency($this->iso_code);

            if (empty($cldrCurrency)) {
                // The currency may not be declared in the locale, eg with custom iso code
                continue;
            }

            $symbol = (string) $cldrCurrency->getSymbol();
            if (empty($symbol)) {
                $symbol = $this->iso_code;
            }
            // symbol is localized
            $namesByLang[$language->id] = $cldrCurrency->getDisplayName();
            $symbolsByLang[$language->id] = $symbol;
        }
        $this->name = $namesByLang;
        $this->symbol = $symbolsByLang;
    }
}
