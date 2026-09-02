<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency;

use Currency;
use Exception;
use Language;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;

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

    /**
     * @param ConfigurationInterface $configuration
     * @param int $shopId
     */
    public function __construct(ConfigurationInterface $configuration, $shopId)
    {
        $this->configuration = $configuration;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
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
        return Currency::findAll(true, false, $currentShopOnly);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllInstalled()
    {
        return Currency::findAllInstalled();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyByIsoCode($isoCode, $idLang = null)
    {
        $currencyId = Currency::getIdByIsoCode($isoCode, 0, false, true);
        if (!$currencyId) {
            return null;
        }

        if (empty($idLang)) {
            $idLang = $this->configuration->get('PS_LANG_DEFAULT');
        }

        return new Currency($currencyId, $idLang);
    }

    /**
     * @param string $isoCode
     * @param string $locale
     *
     * @return Currency|null
     */
    public function getCurrencyByIsoCodeAndLocale($isoCode, $locale)
    {
        $idLang = Language::getIdByLocale($locale, true);

        return $this->getCurrencyByIsoCode($isoCode, $idLang);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyByIsoCodeOrCreate($isoCode, $idLang = null)
    {
        // Soft deleted currencies are not kept duplicated any more, so if one try to recreate it the one in database is reused
        $currency = $this->getCurrencyByIsoCode($isoCode, $idLang);
        if (null === $currency) {
            if (null === $idLang) {
                $idLang = $this->configuration->get('PS_LANG_DEFAULT');
            }
            $currency = new Currency(null, $idLang);
        }

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function saveCurrency(Currency $currencyEntity)
    {
        if (false === $currencyEntity->save()) {
            throw new Exception('Failed saving Currency entity');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyById($currencyId)
    {
        return new Currency($currencyId);
    }

    /**
     * {@inheritdoc}
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
    public function getDefaultCurrency(): Currency
    {
        if (null === $this->defaultCurrency) {
            $this->defaultCurrency = new Currency((int) $this->configuration->get('PS_CURRENCY_DEFAULT'), null, $this->shopId);
        }

        return $this->defaultCurrency;
    }

    public function getDefaultCurrencySymbol(): string
    {
        return $this->getDefaultCurrency()->symbol;
    }
}
