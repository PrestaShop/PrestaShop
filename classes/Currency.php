<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Cldr\Repository;

class CurrencyCore extends ObjectModel
{
    public $id;

    /** @var string Name */
    public $name;

    /** @var string Iso code */
    public $iso_code;

    /** @var string numeric Iso code */
    public $iso_code_num;

    /** @var string exchange rate from euros */
    public $conversion_rate;

    /** @var bool True if currency has been deleted (staying in database as deleted) */
    public $deleted = 0;

    /** @var int bool active */
    public $active;

    public $sign;
    public $format;
    public $blank;
    public $decimals;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'currency',
        'primary' => 'id_currency',
        'multilang_shop' => true,
        'fields' => array(
            'name' =>           array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
            'iso_code' =>       array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 3),
            'conversion_rate' =>array('type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat', 'required' => true, 'shop' => true),
            'deleted' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'active' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    /** @var array Currency cache */
    protected static $currencies = array();
    protected static $countActiveCurrencies = array();

    protected $webserviceParameters = array(
        'objectsNodeName' => 'currencies',
    );

    /**
     * contains the sign to display before price, according to its format
     * @var string
     */
    public $prefix = null;
    /**
     * contains the sign to display after price, according to its format
     * @var string
     */
    public $suffix = null;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->cldr = Tools::getCldr(Context::getContext());

        parent::__construct($id, $id_lang, $id_shop);

        if ($this->iso_code) {
            $cldrCurrency = $this->cldr->getCurrency($this->iso_code);

            $this->sign = $cldrCurrency['symbol'];
            $this->iso_code_num = $cldrCurrency['iso_code'];
            $this->name = $cldrCurrency['name'];
            $this->format = $this->cldr->getCurrencyFormatPattern();
            $this->blank = 1;
            $this->decimals = 1;
        }

        if (!$this->conversion_rate) {
            $this->conversion_rate = 1;
        }
    }

    /**
     * Overriding check if currency rate is not empty and if currency with the same iso code already exists.
     * If it's true, currency is not added.
     *
     * @see ObjectModelCore::add()
     */
    public function add($autodate = true, $nullValues = false)
    {
        if ((float)$this->conversion_rate <= 0) {
            return false;
        }

        $cldrCurrency = $this->cldr->getCurrency($this->iso_code);
        $this->name = $cldrCurrency['name'];

        return Currency::exists($this->iso_code) ? false : parent::add($autodate, $nullValues);
    }

    public function update($autodate = true, $nullValues = false)
    {
        if ((float)$this->conversion_rate <= 0) {
            return false;
        }

        $cldrCurrency = $this->cldr->getCurrency($this->iso_code);
        $this->name = $cldrCurrency['name'];

        return parent::update($autodate, $nullValues);
    }

    /**
     * Check if a curency already exists.
     *
     * @param int|string $iso_code int for iso code number string for iso code
     * @return bool
     */
    public static function exists($iso_code, $id_shop = 0)
    {
        $id_currency_exists = Currency::getIdByIsoCode($iso_code, (int)$id_shop);

        if ($id_currency_exists) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteSelection($selection)
    {
        if (!is_array($selection)) {
            return false;
        }

        $res = array();
        foreach ($selection as $id) {
            $obj = new Currency((int)$id);
            $res[$id] = $obj->delete();
        }

        foreach ($res as $value) {
            if (!$value) {
                return false;
            }
        }
        return true;
    }

    public function delete()
    {
        if ($this->id == Configuration::get('PS_CURRENCY_DEFAULT')) {
            $result = Db::getInstance()->getRow('SELECT `id_currency` FROM '._DB_PREFIX_.'currency WHERE `id_currency` != '.(int)($this->id).' AND `deleted` = 0');
            if (!$result['id_currency']) {
                return false;
            }
            Configuration::updateValue('PS_CURRENCY_DEFAULT', $result['id_currency']);
        }
        $this->deleted = 1;

        // Remove currency restrictions
        $res = Db::getInstance()->delete('module_currency', 'id_currency = '.(int)$this->id);

        return $res && $this->update();
    }

    /**
     * Return formated sign
     *
     * @param string $side left or right
     * @return string formated sign
     */
    public function getSign($side = null)
    {
        return $this->sign;
    }

    /**
     * Return available currencies
     *
     * @return array Currencies
     */
    public static function getCurrencies($object = false, $active = true, $group_by = false)
    {
        $tab = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'currency` c
		'.Shop::addSqlAssociation('currency', 'c').
            ' WHERE `deleted` = 0'.
            ($active ? ' AND c.`active` = 1' : '').
            ($group_by ? ' GROUP BY c.`id_currency`' : '').
            ' ORDER BY `iso_code` ASC');

        return self::addCldrDatasToCurrency($tab, $object);
    }

    public static function getCurrenciesByIdShop($id_shop = 0)
    {
        $currencies = Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'currency` c
		LEFT JOIN `'._DB_PREFIX_.'currency_shop` cs ON (cs.`id_currency` = c.`id_currency`)
		'.($id_shop ? ' WHERE cs.`id_shop` = '.(int)$id_shop : '').'
		ORDER BY `iso_code` ASC');

        return self::addCldrDatasToCurrency($currencies);
    }

    /*
     * Add Cldr datas to result query or signe object/array
     *
     * @param $currencies mixed object|array
     * @param $isObject bool
     */
    protected static function addCldrDatasToCurrency($currencies, $isObject = false)
    {
        $cldr = new Repository(Context::getContext()->language);

        if (is_array($currencies)) {
            foreach ($currencies as $k => $c) {
                if ($isObject) {
                    $currencies[$k] = Currency::getCurrencyInstance($c['id_currency']);
                } else {
                    $currency = $cldr->getCurrency($c['iso_code']);

                    $currencies[$k]['name'] = ucfirst($currency['name']);
                    $currencies[$k]['iso_code_num'] = $currency['iso_code'];
                    $currencies[$k]['sign'] = $currency['symbol'];
                    $currencies[$k]['format'] = '';
                }
            }
        } else {
            if ($isObject) {
                $currencies = Currency::getCurrencyInstance($currencies['id_currency']);
            } else {
                $currency = $cldr->getCurrency($currencies['iso_code']);

                $currencies['name'] = ucfirst($currency['name']);
                $currencies['iso_code_num'] = $currency['iso_code'];
                $currencies['sign'] = $currency['symbol'];
                $currencies['format'] = '';
            }
        }

        return $currencies;
    }

    public static function getPaymentCurrenciesSpecial($id_module, $id_shop = null)
    {
        if (is_null($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT *
				FROM '._DB_PREFIX_.'module_currency
				WHERE id_module = '.(int)$id_module.'
					AND id_shop ='.(int)$id_shop;
        return Db::getInstance()->getRow($sql);
    }

    public static function getPaymentCurrencies($id_module, $id_shop = null)
    {
        if (is_null($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT c.*
				FROM `'._DB_PREFIX_.'module_currency` mc
				LEFT JOIN `'._DB_PREFIX_.'currency` c ON c.`id_currency` = mc.`id_currency`
				WHERE c.`deleted` = 0
					AND mc.`id_module` = '.(int)$id_module.'
					AND c.`active` = 1
					AND mc.id_shop = '.(int)$id_shop.'
				ORDER BY c.`iso_code` ASC';

        return Db::getInstance()->executeS($sql);
    }

    public static function checkPaymentCurrencies($id_module, $id_shop = null)
    {
        if (empty($id_module)) {
            return false;
        }

        if (is_null($id_shop)) {
            $id_shop = Context::getContext()->shop->id;
        }

        $sql = 'SELECT *
				FROM `'._DB_PREFIX_.'module_currency`
				WHERE `id_module` = '.(int)$id_module.'
					AND `id_shop` = '.(int)$id_shop;


        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public static function getCurrency($id_currency)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'currency`
		WHERE `deleted` = 0
		AND `id_currency` = '.(int)($id_currency));
    }

    /**
     * @param $iso_code
     * @param int $id_shop
     * @return int
     */
    public static function getIdByIsoCode($iso_code, $id_shop = 0)
    {
        $cache_id = 'Currency::getIdByIsoCode_'.pSQL($iso_code).'-'.(int)$id_shop;
        if (!Cache::isStored($cache_id)) {
            $query = Currency::getIdByQuery($id_shop);
            $query->where('iso_code = \''.pSQL($iso_code).'\'');

            $result = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * @param int $id_shop
     * @return DbQuery
     */
    public static function getIdByQuery($id_shop = 0)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('deleted = 0');

        if (Shop::isFeatureActive() && $id_shop > 0) {
            $query->leftJoin('currency_shop', 'cs', 'cs.id_currency = c.id_currency');
            $query->where('id_shop = '.(int)$id_shop);
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
        $exchange_rate = 1;
        $tmp = $this->conversion_rate;
        if ($defaultCurrency->iso_code != $isoCodeSource) {
            foreach ($data->currency as $currency) {
                if ($currency['iso_code'] == $defaultCurrency->iso_code) {
                    $exchange_rate = round((float)$currency['rate'], 6);
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
                    if ($this->iso_code == strval($obj['iso_code'])) {
                        $rate = (float)$obj['rate'];
                        break;
                    }
                }
            }

            if (isset($rate)) {
                $this->conversion_rate = round($rate / $exchange_rate, 6);
            }
        }

        if ($tmp != $this->conversion_rate) {
            $this->update();
        }
    }

    public static function getDefaultCurrency()
    {
        $id_currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        if ($id_currency == 0) {
            return false;
        }

        return new Currency($id_currency);
    }

    public static function refreshCurrencies()
    {
        // Parse
        if (!$feed = Tools::simplexml_load_file(_PS_CURRENCY_FEED_URL_)) {
            return Tools::displayError('Cannot parse feed.');
        }

        // Default feed currency (EUR)
        $isoCodeSource = strval($feed->source['iso_code']);

        if (!$default_currency = Currency::getDefaultCurrency()) {
            return Tools::displayError('No default currency');
        }

        $currencies = Currency::getCurrencies(true, false, true);
        foreach ($currencies as $currency) {
            /** @var Currency $currency */
            if ($currency->id != $default_currency->id) {
                $currency->refreshCurrency($feed->list, $isoCodeSource, $default_currency);
            }
        }
    }

    /**
     * Get current currency
     *
     * @deprecated as of 1.5 use $context->currency instead
     * @return Currency
     */
    public static function getCurrent()
    {
        Tools::displayAsDeprecated();
        return Context::getContext()->currency;
    }

    public static function getCurrencyInstance($id)
    {
        if (!isset(self::$currencies[$id])) {
            self::$currencies[(int)($id)] = new Currency($id);
        }
        return self::$currencies[(int)($id)];
    }

    public function getConversationRate()
    {
        return $this->id != (int)Configuration::get('PS_CURRENCY_DEFAULT') ? $this->conversion_rate : 1;
    }

    public static function countActiveCurrencies($id_shop = null)
    {
        if ($id_shop === null) {
            $id_shop = (int)Context::getContext()->shop->id;
        }

        if (!isset(self::$countActiveCurrencies[$id_shop])) {
            self::$countActiveCurrencies[$id_shop] = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT c.id_currency) FROM `'._DB_PREFIX_.'currency` c
				LEFT JOIN '._DB_PREFIX_.'currency_shop cs ON (cs.id_currency = c.id_currency AND cs.id_shop = '.(int)$id_shop.')
				WHERE c.`active` = 1
			');
        }
        return self::$countActiveCurrencies[$id_shop];
    }

    public static function isMultiCurrencyActivated($id_shop = null)
    {
        return (Currency::countActiveCurrencies($id_shop) > 1);
    }
}
