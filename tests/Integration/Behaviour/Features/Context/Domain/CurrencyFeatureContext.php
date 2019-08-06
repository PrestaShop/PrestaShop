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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Currency;
use DbQuery;
use Db;
use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CurrencyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new currency :reference with following properties:
     */
    public function addCurrency($reference, TableNode $node)
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');

        $data = $node->getRowsHash();
        /** @var \Shop $shop */
        $shop = SharedStorage::getStorage()->get($data['shop_association']);

        $command = new AddCurrencyCommand(
            $data['iso_code'],
            (int) $data['numeric_iso_code'],
            (float) $data['exchange_rate'],
            [$defaultLangId => $data['name']],
            [$defaultLangId => $data['symbol']],
            (bool) $data['is_enabled']
        );

        $command->setShopIds([
            (int) $shop->id,
        ]);

        try {
            $this->lastException = null;
            /** @var CurrencyId $currencyId */
            $currencyId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Currency($currencyId->getValue()));
        } catch (CoreException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I edit currency :reference with following properties:
     */
    public function editCurrency($reference, TableNode $node)
    {
        $defaultLangId = Configuration::get('PS_LANG_DEFAULT');

        $data = $node->getRowsHash();
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        $command = new EditCurrencyCommand((int) $currency->id);

        if (isset($data['iso_code'])) {
            $command->setIsoCode($data['iso_code']);
        }

        if (isset($data['numeric_iso_code'])) {
            $command->setNumericIsoCode((int) $data['numeric_iso_code']);
        }

        if (isset($data['exchange_rate'])) {
            $command->setExchangeRate((float) $data['exchange_rate']);
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

        $this->getCommandBus()->handle($command);
        try {
            $this->lastException = null;
            $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Currency($currency->id));
        } catch (CoreException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I disable currency ":currencyReference"
     */
    public function disableCurrency($reference)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        try {
            $this->lastException = null;
            $this->getCommandBus()->handle(new ToggleCurrencyStatusCommand((int) $currency->id));
        } catch (CannotDisableDefaultCurrencyException $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When I delete currency ":currencyReference"
     */
    public function deleteCurrency($reference)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        try {
            $this->lastException = null;
            $this->getCommandBus()->handle(new DeleteCurrencyCommand((int) $currency->id));
        } catch (CannotDeleteDefaultCurrencyException $e) {
            $this->lastException = $e;
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
}
