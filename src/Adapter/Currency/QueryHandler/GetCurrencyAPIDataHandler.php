<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyAPIData;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyExchangeRate;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyAPIDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\CurrencyAPIData;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ExchangeRate as ExchangeRateResult;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\ExchangeRate;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

class GetCurrencyAPIDataHandler implements GetCurrencyAPIDataHandlerInterface
{
    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var LanguageInterface[]
     */
    private $languages;

    /**
     * @param LocaleRepository $localeRepository
     * @param CommandBusInterface $queryBus
     * @param array $languages
     */
    public function __construct(
        LocaleRepository $localeRepository,
        CommandBusInterface $queryBus,
        array $languages
    ) {
        $this->localeRepository = $localeRepository;
        $this->queryBus = $queryBus;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyAPIData $query): CurrencyAPIData
    {
        $localizedNames = [];
        $localizedSymbols = [];
        $currency = null;
        /** @var LanguageInterface $language */
        foreach ($this->languages as $language) {
            $locale = $this->localeRepository->getLocale($language->getLocale());
            $localeCurrency = $locale->getCurrency($query->getIsoCode()->getValue());
            if (null !== $localeCurrency) {
                $currency = $localeCurrency;
                $localizedNames[$language->getId()] = $localeCurrency->getDisplayName();
                $localizedSymbols[$language->getId()] = $localeCurrency->getSymbol(CurrencyInterface::SYMBOL_TYPE_NARROW) ?: $localeCurrency->getIsoCode();
            } else {
                $localizedNames[$language->getId()] = $query->getIsoCode()->getValue();
                $localizedSymbols[$language->getId()] = $query->getIsoCode()->getValue();
            }
        }

        if (null === $currency) {
            return new CurrencyAPIData(
                $query->getIsoCode()->getValue(),
                null,
                $localizedNames,
                $localizedSymbols,
                ExchangeRate::getDefaultExchangeRate(),
                2
            );
        }

        try {
            /** @var ExchangeRateResult $exchangeRateResult */
            $exchangeRateResult = $this->queryBus->handle(new GetCurrencyExchangeRate($query->getIsoCode()->getValue()));
            /** @var Number $exchangeRate */
            $exchangeRate = $exchangeRateResult->getValue();
        } catch (CurrencyException $e) {
            //Unable to find the exchange rate, either the currency doesn't exist (unofficial)
            //or the currency feed could not be fetched, use the default rate as a fallback
            $exchangeRate = ExchangeRate::getDefaultExchangeRate();
        }

        return new CurrencyAPIData(
            $currency->getIsoCode(),
            $currency->getNumericIsoCode(),
            $localizedNames,
            $localizedSymbols,
            $exchangeRate,
            $currency->getDecimalDigits()
        );
    }
}
