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

namespace PrestaShop\PrestaShop\Adapter\Currency;

use Currency;
use Exception;
use Language;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopException;

/**
 * This class will provide data from DB / ORM about Currency.
 */
class CurrencyDataProvider implements CurrencyDataProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var int
     */
    private $shopId;

    /** @var Currency */
    private $defaultCurrency;

    public function __construct(ConfigurationInterface $configuration, $shopId)
    {
        $this->configuration = $configuration;
        $this->shopId = $shopId;
    }

    /**
     * Return available currencies.
     *
     * @return array Currencies
     */
    public function getCurrencies($object = false, $active = true, $group_by = false)
    {
        return Currency::getCurrencies($object = false, $active = true, $group_by = false);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll($currentShopOnly = true)
    {
        return Currency::findAll(false, false, $currentShopOnly);
    }

    /**
     * Get a Currency entity instance by ISO code.
     *
     * @param string $isoCode
     *                        An ISO 4217 currency code
     * @param int|false|null $idLang
     *                               Set this parameter if you want the currency in a specific language.
     *                               If null or false, default language will be used
     *
     * @return currency|null
     *                       The asked Currency object, or null if not found
     */
    public function getCurrencyByIsoCode($isoCode, $idLang = null)
    {
        $currencyId = Currency::getIdByIsoCode($isoCode);
        if (!$currencyId) {
            return null;
        }

        if (empty($idLang)) {
            $idLang = $this->configuration->get('PS_LANG_DEFAULT');
        }

        return new Currency($currencyId, $idLang);
    }

    /**
     * Get a Currency entity instance by ISO code.
     *
     * @param string $isoCode
     *                        An ISO 4217 currency code
     * @param string $locale
     *                       The locale to use for localized data
     *
     * @return currency|null
     *                       The asked Currency object, or null if not found
     */
    public function getCurrencyByIsoCodeAndLocale($isoCode, $locale)
    {
        $idLang = Language::getIdByLocale($locale);

        return $this->getCurrencyByIsoCode($isoCode, $idLang);
    }

    /**
     * Get a Currency entity instance.
     * If the passed ISO code is known, this Currency entity will be loaded with known data.
     *
     * @param string $isoCode
     *                        An ISO 4217 currency code
     * @param int|null $idLang
     *                         Set this parameter if you want the currency in a specific language.
     *                         If null, default language will be used
     *
     * @return currency
     *                  The asked Currency object, loaded with relevant data if passed ISO code is known
     */
    public function getCurrencyByIsoCodeOrCreate($isoCode, $idLang = null)
    {
        if (null === $idLang) {
            $idLang = $this->configuration->get('PS_LANG_DEFAULT');
        }

        $currency = $this->getCurrencyByIsoCode($isoCode, $idLang);
        if (null === $currency) {
            $currency = new Currency(null, $idLang);
        }

        return $currency;
    }

    /**
     * Persists a Currency entity into DB.
     * If this entity already exists in DB (has a known currency_id), it will be updated.
     *
     * @param Currency $currencyEntity
     *                                 Currency object model to save
     *
     * @throws PrestaShopException
     *                             If something wrong happened with DB when saving $currencyEntity
     * @throws Exception
     *                   If an unexpected result is retrieved when saving $currencyEntity
     */
    public function saveCurrency(Currency $currencyEntity)
    {
        if (false === $currencyEntity->save()) {
            throw new Exception('Failed saving Currency entity');
        }
    }

    /**
     * Gets a legacy Currency instance by ID.
     *
     * @param int $currencyId
     *
     * @return Currency
     */
    public function getCurrencyById($currencyId)
    {
        return new Currency($currencyId);
    }

    /**
     * Get Default currency Iso code.
     */
    public function getDefaultCurrencyIsoCode()
    {
        return $this->getDefaultCurrency()->iso_code;
    }

    /**
     * Returns default Currency set in Configuration
     *
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        if (null === $this->defaultCurrency) {
            $this->defaultCurrency = new Currency((int) $this->configuration->get('PS_CURRENCY_DEFAULT'), null, $this->shopId);
        }

        return $this->defaultCurrency;
    }
}
