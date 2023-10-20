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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function getDefaultCurrency()
    {
        if (null === $this->defaultCurrency) {
            $this->defaultCurrency = new Currency((int) $this->configuration->get('PS_CURRENCY_DEFAULT'), null, $this->shopId);
        }

        return $this->defaultCurrency;
    }
}
