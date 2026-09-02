<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetReferenceCurrency;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetReferenceCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ReferenceCurrency;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

#[AsQueryHandler]
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
