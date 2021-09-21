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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetReferenceCurrency;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\ReferenceCurrency;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CurrencyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer which should never exist in test database as currency id
     */
    private const NON_EXISTING_CURRENCY_ID = 1234567;

    /**
     * @var ReferenceCurrency
     */
    private $currencyData;

    /**
     * @When I add new currency :reference with following properties:
     */
    public function addCurrency($reference, TableNode $node)
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');

        $data = $this->localizeByRows($node);
        $shopId = SharedStorage::getStorage()->get($data['shop_association']);

        if ($data['is_unofficial']) {
            $command = new AddUnofficialCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                (bool) $data['is_enabled']
            );
        } else {
            $command = new AddCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                (bool) $data['is_enabled']
            );
        }

        if (isset($data['precision'])) {
            $command->setPrecision((int) $data['precision']);
        }

        if (isset($data['name'])) {
            $command->setLocalizedNames([$defaultLangId => $data['name']]);
        }

        if (isset($data['symbol'])) {
            $command->setLocalizedSymbols([$defaultLangId => $data['symbol']]);
        }

        if (isset($data['transformations'])) {
            $command->setLocalizedTransformations($data['transformations']);
        }

        $command->setShopIds([$shopId]);

        try {
            /** @var CurrencyId $currencyId */
            $currencyId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, $currencyId->getValue());
        } catch (CoreException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit currency :reference with following properties:
     */
    public function editCurrency($reference, TableNode $node)
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');

        $data = $this->localizeByRows($node);
        $currency = $this->getCurrency($reference);

        if (!empty($data['is_unofficial'])) {
            $command = new EditUnofficialCurrencyCommand((int) $currency->id);
            if (isset($data['iso_code'])) {
                $command->setIsoCode($data['iso_code']);
            }
        } else {
            $command = new EditCurrencyCommand((int) $currency->id);
        }

        if (isset($data['exchange_rate'])) {
            $command->setExchangeRate((float) $data['exchange_rate']);
        }

        if (isset($data['precision'])) {
            $command->setPrecision((int) $data['precision']);
        }

        if (isset($data['is_enabled'])) {
            $command->setIsEnabled((bool) $data['is_enabled']);
        }

        if (isset($data['shop_association'])) {
            $command->setShopIds([(int) $data['shop_association']]);
        }

        if (isset($data['name'])) {
            $command->setLocalizedNames([$defaultLangId => $data['name']]);
        }

        if (isset($data['symbol'])) {
            $command->setLocalizedSymbols([$defaultLangId => $data['symbol']]);
        }

        if (isset($data['transformations'])) {
            $command->setLocalizedTransformations($data['transformations']);
        }

        try {
            $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, (int) $currency->id);
        } catch (CoreException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I disable currency ":currencyReference"
     */
    public function disableCurrency($reference)
    {
        $currency = $this->getCurrency($reference);

        try {
            $this->getCommandBus()->handle(new ToggleCurrencyStatusCommand((int) $currency->id));
        } catch (CannotDisableDefaultCurrencyException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete currency ":currencyReference"
     */
    public function deleteCurrency($reference)
    {
        $currency = $this->getCurrency($reference);

        try {
            $this->getCommandBus()->handle(new DeleteCurrencyCommand((int) $currency->id));
        } catch (CannotDeleteDefaultCurrencyException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given currency :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingCurrencyReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getCurrency($reference)->id) {
            throw new RuntimeException(sprintf('Expected that currency "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_CURRENCY_ID);
    }

    /**
     * @When I request reference data for :currencyIsoCode
     */
    public function getCurrencyReferenceData($currencyIsoCode)
    {
        try {
            $this->currencyData = $this->getCommandBus()->handle(new GetReferenceCurrency($currencyIsoCode));
        } catch (CurrencyException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get currency data:
     */
    public function checkCurrencyData(TableNode $node)
    {
        $apiData = [
            'iso_code' => $this->currencyData->getIsoCode(),
            'numeric_iso_code' => $this->currencyData->getNumericIsoCode(),
            'precision' => $this->currencyData->getPrecision(),
            'names' => $this->currencyData->getNames(),
            'symbols' => $this->currencyData->getSymbols(),
            'patterns' => $this->currencyData->getPatterns(),
        ];
        $expectedData = $this->localizeByRows($node);
        $expectedData['names'] = $expectedData['names'];
        $expectedData['symbols'] = $expectedData['symbols'];
        $expectedData['patterns'] = $expectedData['patterns'];

        foreach ($expectedData as $key => $expectedValue) {
            if ($expectedValue === 'null') {
                $expectedValue = null;
            }

            if ($expectedValue != $apiData[$key]) {
                throw new RuntimeException(sprintf('Invalid currency data field %s: %s expected %s', $key, json_encode($apiData[$key]), json_encode($expectedValue)));
            }
        }
    }

    /**
     * @Then I should get error that default currency cannot be disabled
     */
    public function assertLastErrorIsDefaultCurrencyCannotBeDisabled()
    {
        $this->assertLastErrorIs(CannotDisableDefaultCurrencyException::class);
    }

    /**
     * @Then I should get error that default currency cannot be deleted
     */
    public function assertLastErrorIsDefaultCurrencyCannotBeDeleted()
    {
        $this->assertLastErrorIs(CannotDeleteDefaultCurrencyException::class);
    }

    /**
     * @Then /^I should get error that unofficial currency is invalid$/
     */
    public function assertLastErrorIsInvalidUnofficialCurrency()
    {
        $this->assertLastErrorIs(InvalidUnofficialCurrencyException::class);
    }

    /**
     * @Then /^I should get error that currency name is invalid$/
     */
    public function assertLastErrorIsInvalidCurrencyName()
    {
        $this->assertLastErrorIs(CurrencyConstraintException::class, CurrencyConstraintException::INVALID_NAME);
    }

    /**
     * @Then I should get error that currency already exists
     */
    public function assertLastErrorIsCurrencyAlreadyExists()
    {
        $this->assertLastErrorIs(CurrencyConstraintException::class, CurrencyConstraintException::CURRENCY_ALREADY_EXISTS);
    }

    /**
     * @Then I should get error that currency was not found
     */
    public function assertLastErrorIsNotFound()
    {
        $this->assertLastErrorIs(CurrencyNotFoundException::class);
    }

    /**
     * @param string $reference
     *
     * @return Currency
     */
    private function getCurrency(string $reference): Currency
    {
        return new Currency($this->getSharedStorage()->get($reference));
    }
}
