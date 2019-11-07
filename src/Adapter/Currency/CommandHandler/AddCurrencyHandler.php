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

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\AddCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidCustomCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShopException;

/**
 * Class AddCurrencyHandler is responsible for adding new currency.
 *
 * @internal
 */
final class AddCurrencyHandler extends AbstractCurrencyHandler implements AddCurrencyHandlerInterface
{
    /**
     * @var Language
     */
    private $defaultLanguage;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param Language $defaultLanguage
     */
    public function __construct(LocaleRepository $localeRepoCLDR, $defaultLanguageId)
    {
        parent::__construct($localeRepoCLDR);
        $this->defaultLanguage = new Language((int) $defaultLanguageId);
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(AddCurrencyCommand $command)
    {
        if ($command->isCustom()) {
            $this->assertCustomCurrencyDoesNotMatchAnyIsoCode($command->getIsoCode()->getValue());
            $this->assertCustomCurrencyDoesNotMatchAnyNumericIsoCode($command->getNumericIsoCode()->getValue());
        } else {
            $this->assertIsoCodesAreMatching($command);
        }
        $this->assertCurrencyWithIsoCodeDoesNotExist($command->getIsoCode()->getValue());
        $this->assertCurrencyWithNumericIsoCodeDoesNotExist($command->getNumericIsoCode()->getValue());

        try {
            $entity = new Currency();

            $entity->iso_code = $command->getIsoCode()->getValue();
            $entity->numeric_iso_code = $command->getNumericIsoCode()->getValue();
            $entity->active = $command->isEnabled();
            $entity->custom = $command->isCustom();
            $entity->conversion_rate = $command->getExchangeRate()->getValue();

            // CLDR locale give us the CLDR reference specification
            $cldrLocale = $this->localeRepoCLDR->getLocale($this->defaultLanguage->getLocale());
            // CLDR currency gives data from CLDR reference, for the given language
            $cldrCurrency = $cldrLocale->getCurrency($entity->iso_code);
            if (!empty($cldrCurrency)) {
                // The currency may not be declared in the locale, eg with custom iso code
                $entity->precision = (int) $cldrCurrency->getDecimalDigits();
                $entity->numeric_iso_code = $cldrCurrency->getNumericIsoCode();
            }

            if (!empty($command->getLocalizedNames())) {
                $entity->name = $entity->names = $command->getLocalizedNames();
            }
            if (!empty($command->getLocalizedSymbols())) {
                $entity->symbol = $entity->symbols = $command->getLocalizedSymbols();
            }
            //This method will insert the missing localized names/symbols and detect if the currency has been edited
            $entity->refreshLocalizedCurrencyData(Language::getLanguages(), $this->localeRepoCLDR);

            if (false === $entity->add()) {
                throw new CannotCreateCurrencyException('Failed to create new currency');
            }

            $this->associateWithShops($entity, $command->getShopIds());
            $this->associateConversionRateToShops($entity, $command->getShopIds());
        } catch (PrestaShopException $exception) {
            throw new CurrencyException('Failed to create new currency', 0, $exception);
        }

        return new CurrencyId((int) $entity->id);
    }

    /**
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertCurrencyWithIsoCodeDoesNotExist($isoCode)
    {
        if (Currency::exists($isoCode)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with iso code "%s" already exist and cannot be created',
                    $isoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }

    /**
     * @param int $numericIsoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertCurrencyWithNumericIsoCodeDoesNotExist($numericIsoCode)
    {
        if (Currency::getIdByNumericIsoCode($numericIsoCode)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with numeric iso code "%s" already exist and cannot be created',
                    $numericIsoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }

    /**
     * @param AddCurrencyCommand $command
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsoCodesAreMatching(AddCurrencyCommand $command)
    {
        //Numeric is code will be deduced from iso code, only check real currencies
        if (null === $command->getNumericIsoCode() || $command->isCustom()) {
            return;
        }

        $defaultLocaleCLDR = $this->localeRepoCLDR->getLocale($this->defaultLanguage->getLocale());
        $allCurrencies = $defaultLocaleCLDR->getAllCurrencies();
        $matchingRealCurrency = null;
        foreach ($allCurrencies as $currencyData) {
            if ($currencyData->getIsoCode() == $command->getIsoCode()->getValue() &&
                $currencyData->getNumericIsoCode() == $command->getNumericIsoCode()->getValue()) {
                $matchingRealCurrency = $currencyData;
                break;
            }
        }

        if (null === $matchingRealCurrency) {
            throw new CurrencyConstraintException(
                sprintf(
                    'The is no real currency matching iso code %s and numeric iso code %s',
                    $command->getIsoCode()->getValue(),
                    $command->getNumericIsoCode()->getValue()
                ),
                CurrencyConstraintException::MISMATCHING_ISO_CODES
            );
        }
    }
}
