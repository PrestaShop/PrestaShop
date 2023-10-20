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

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\AddCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Locale;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Adds a new currency.
 *
 * @internal
 */
final class AddOfficialCurrencyHandler extends AbstractCurrencyHandler implements AddCurrencyHandlerInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     * @param CurrencyDataProviderInterface $currencyDataProvider
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages,
        CurrencyCommandValidator $validator,
        CurrencyDataProviderInterface $currencyDataProvider,
        PatternTransformer $patternTransformer
    ) {
        parent::__construct($localeRepoCLDR, $languages, $validator, $patternTransformer);
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotCreateCurrencyException
     * @throws CurrencyConstraintException
     * @throws CurrencyException
     * @throws CurrencyNotFoundException
     * @throws LocalizationException
     * @throws LanguageNotFoundException
     */
    public function handle(AddCurrencyCommand $command)
    {
        $this->validator->assertCurrencyIsNotAvailableInDatabase($command->getIsoCode()->getValue());

        try {
            $entity = $this->currencyDataProvider->getCurrencyByIsoCodeOrCreate($command->getIsoCode()->getValue());

            $entity->unofficial = false;
            $entity->numeric_iso_code = $this->findNumericIsoCodeFromAlphaCode($command->getIsoCode()->getValue());
            $entity->precision = $this->getPrecision($command);

            $this->addEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException('Failed to create new currency', 0, $exception);
        }

        return new CurrencyId((int) $entity->id);
    }

    /**
     * @param AddCurrencyCommand $command
     *
     * @return int
     */
    private function getPrecision(AddCurrencyCommand $command): int
    {
        if (null !== $command->getPrecision()) {
            return $command->getPrecision()->getValue();
        }

        // CLDR locale give us the CLDR reference specification
        $cldrLocale = $this->getCLDRLocale();
        // CLDR currency gives data from CLDR reference, for the given language
        $cldrCurrency = $cldrLocale->getCurrency($command->getIsoCode()->getValue());

        return $cldrCurrency->getDecimalDigits();
    }

    /**
     * @param string $isoCode
     *
     * @return string
     *
     * @throws CurrencyNotFoundException
     */
    private function findNumericIsoCodeFromAlphaCode($isoCode): string
    {
        $cldrLocale = $this->getCLDRLocale();
        $allCurrencies = $cldrLocale->getAllCurrencies();

        foreach ($allCurrencies as $currencyData) {
            if ($currencyData->getIsoCode() === $isoCode) {
                return $currencyData->getNumericIsoCode();
            }
        }

        throw new CurrencyNotFoundException(sprintf('ISO code "%s" does not match any currency in CLDR database', $isoCode));
    }

    /**
     * Returns a CLDR locale, since they all contain the same info about currencies
     * it doesn't matter which one is used so we return the one matching the first
     * provided language.
     *
     * @return Locale
     */
    private function getCLDRLocale()
    {
        /** @var LanguageInterface $language */
        $language = $this->languages[0];

        return $this->localeRepoCLDR->getLocale($language->getLocale());
    }
}
