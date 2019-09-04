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

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\AddUnofficialCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShopException;

/**
 * Class AddUnofficialCurrencyHandler is responsible for adding new unofficial currency.
 *
 * @internal
 */
final class AddUnofficialCurrencyHandler extends AbstractAddCurrencyHandler implements AddUnofficialCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(AddUnofficialCurrencyCommand $command)
    {
        $this->assertUnofficialCurrencyDoesNotMatchAnyRealIsoCode($command->getIsoCode()->getValue());
        if (null !== $command->getNumericIsoCode()) {
            $this->assertUnofficialCurrencyDoesNotMatchAnyRealNumericIsoCode($command->getNumericIsoCode()->getValue());
        }

        $this->assertCurrencyWithIsoCodeDoesNotExist($command);
        $this->assertCurrencyWithNumericIsoCodeDoesNotExist($command);

        try {
            $entity = new Currency();

            $entity->iso_code = $command->getIsoCode()->getValue();
            $entity->active = $command->isEnabled();
            $entity->unofficial = true;
            $entity->conversion_rate = $command->getExchangeRate()->getValue();
            if (null !== $command->getNumericIsoCode()) {
                $entity->numeric_iso_code = $command->getNumericIsoCode()->getValue();
            } else {
                $entity->numeric_iso_code = $this->getRandomNumericIsoCode();
            }
            if (null !== $command->getPrecision()) {
                $entity->precision = $command->getPrecision()->getValue();
            }

            if (!empty($command->getLocalizedNames())) {
                $entity->setNames($command->getLocalizedNames());
            }
            if (!empty($command->getLocalizedSymbols())) {
                $entity->setSymbols($command->getLocalizedSymbols());
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
     * @return int
     */
    private function getRandomNumericIsoCode()
    {
        $defaultLocaleCLDR = $this->localeRepoCLDR->getLocale($this->defaultLanguage->getLocale());
        $allCurrencies = $defaultLocaleCLDR->getAllCurrencies();

        $realNumericIsoCodes = [];
        foreach ($allCurrencies as $currencyData) {
            if (!empty($currencyData->getNumericIsoCode()) && is_int($currencyData->getNumericIsoCode())) {
                $realNumericIsoCodes[] = (int) $currencyData->getNumericIsoCode();
            }
        }

        $databaseCurrencies = Currency::findAll(false);
        $databaseNumericIsoCodes = [];
        foreach ($databaseCurrencies as $databaseCurrency) {
            if (!empty($databaseCurrency['numeric_iso_code']) && is_int($databaseCurrency['numeric_iso_code'])) {
                $databaseNumericIsoCodes[] = $databaseCurrency['numeric_iso_code'];
            }
        }

        $allowedNumericIsoCodes = [];
        for ($i = 1; $i < 1000; ++$i) {
            if (!in_array($i, $realNumericIsoCodes) && !in_array($i, $databaseNumericIsoCodes)) {
                $allowedNumericIsoCodes[] = $i;
            }
        }

        return $allowedNumericIsoCodes[rand(0, count($allowedNumericIsoCodes) - 1)];
    }
}
