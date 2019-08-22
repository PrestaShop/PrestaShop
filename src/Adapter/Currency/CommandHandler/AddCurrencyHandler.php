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
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShopException;

/**
 * Class AddCurrencyHandler is responsible for adding new currency.
 *
 * @internal
 */
final class AddCurrencyHandler extends AbstractAddCurrencyHandler implements AddCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(AddCurrencyCommand $command)
    {
        $this->assertIsoCodesAreMatching($command);
        $this->assertCurrencyWithIsoCodeDoesNotExist($command);
        $this->assertCurrencyWithNumericIsoCodeDoesNotExist($command);

        try {
            $entity = new Currency();

            $entity->iso_code = $command->getIsoCode()->getValue();
            $entity->active = $command->isEnabled();
            $entity->unofficial = false;
            $entity->conversion_rate = $command->getExchangeRate()->getValue();
            if (null !== $command->getNumericIsoCode()) {
                $entity->numeric_iso_code = $command->getNumericIsoCode()->getValue();
            } else {
                $entity->numeric_iso_code = $this->getNumericIsoCode($command->getIsoCode()->getValue());
            }

            if (null !== $command->getPrecision()) {
                $entity->precision = $command->getPrecision()->getValue();
            } else {
                // CLDR locale give us the CLDR reference specification
                $cldrLocale = $this->localeRepoCLDR->getLocale($this->defaultLanguage->getLocale());
                // CLDR currency gives data from CLDR reference, for the given language
                $cldrCurrency = $cldrLocale->getCurrency($entity->iso_code);
                $entity->precision = (int) $cldrCurrency->getDecimalDigits();
                $entity->numeric_iso_code = $cldrCurrency->getNumericIsoCode();
            }

            if (!empty($command->getLocalizedNames())) {
                $entity->name = $entity->names = $command->getLocalizedNames();
            }
            if (!empty($command->getLocalizedSymbols())) {
                $entity->symbol = $entity->symbols = $command->getLocalizedSymbols();
            }
            //This method will insert the missing localized names/symbols and detect if the currency has been modified
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
     * @return int
     *
     * @throws CurrencyNotFoundException
     */
    private function getNumericIsoCode($isoCode)
    {
        $defaultLocaleCLDR = $this->localeRepoCLDR->getLocale($this->defaultLanguage->getLocale());
        $allCurrencies = $defaultLocaleCLDR->getAllCurrencies();

        $matchingRealCurrency = null;
        foreach ($allCurrencies as $currencyData) {
            if ($currencyData->getIsoCode() == $isoCode) {
                $matchingRealCurrency = $currencyData;
                break;
            }
        }

        if (null === $matchingRealCurrency) {
            throw new CurrencyNotFoundException(
                sprintf(
                    'There is no real currency with iso code %s',
                    $isoCode
                )
            );
        }

        return (int) $matchingRealCurrency->getNumericIsoCode();
    }

    /**
     * @param AddCurrencyCommand $command
     *
     * @throws CurrencyConstraintException
     */
    private function assertIsoCodesAreMatching(AddCurrencyCommand $command)
    {
        //Numeric ISO code will be deduced from ISO code, only check real currencies
        if (null === $command->getNumericIsoCode()) {
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
