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

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetReferenceCurrency;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetReferenceCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ReferenceCurrency;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

class GetReferenceCurrencyHandler implements GetReferenceCurrencyHandlerInterface
{
    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var LanguageInterface[]
     */
    private $languages;

    /**
     * @param LocaleRepository $localeRepository
     * @param array $languages
     */
    public function __construct(
        LocaleRepository $localeRepository,
        array $languages
    ) {
        $this->localeRepository = $localeRepository;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetReferenceCurrency $query): ReferenceCurrency
    {
        $localizedNames = [];
        $localizedSymbols = [];
        $localizedPatterns = [];
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
            $localizedPatterns[$language->getId()] = $locale->getCurrencyPattern();
        }

        if (null === $currency) {
            throw new CurrencyNotFoundException(sprintf('Can not find reference currency with ISO code %s', $query->getIsoCode()->getValue()));
        }

        return new ReferenceCurrency(
            $currency->getIsoCode(),
            $currency->getNumericIsoCode(),
            $localizedNames,
            $localizedSymbols,
            $localizedPatterns,
            $currency->getDecimalDigits()
        );
    }
}
