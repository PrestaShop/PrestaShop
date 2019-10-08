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
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CurrencyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new currency :reference with following properties:
     */
    public function addCurrency($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        /** @var \Shop $shop */
        $shop = SharedStorage::getStorage()->get($data['shop_association']);

        $command = new AddCurrencyCommand(
            $data['iso_code'],
            (float) $data['exchange_rate'],
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
        $data = $node->getRowsHash();
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        $command = new EditCurrencyCommand((int) $currency->id);

        if (isset($data['iso_code'])) {
            $command->setIsoCode($data['iso_code']);
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
     * @Then currency :reference should be :isoCode
     */
    public function assertCurrencyIsoCode($reference, $isoCode)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ($currency->iso_code !== $isoCode) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" iso code, but "%s" was expected.',
                $reference,
                $currency->iso_code,
                $isoCode
            ));
        }
    }

    /**
     * @Then /^currency "(.*)" should have status (enabled|disabled)$/
     */
    public function assertCurrencyStatus($reference, $status)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);
        $expectedStatus = $status === 'enabled';

        if ($currency->active != $expectedStatus) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has status "%s", but "%s" was expected.',
                $reference,
                $currency->active,
                $expectedStatus
            ));
        }
    }

    /**
     * @Then currency :reference exchange rate should be :exchangeRate
     */
    public function assertCurrencyExchangeRate($reference, $exchangeRate)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ((float) $currency->conversion_rate != (float) $exchangeRate) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" exchange rate, but "%s" was expected.',
                $reference,
                $currency->conversion_rate,
                $exchangeRate
            ));
        }
    }

    /**
     * @Then currency :reference precision should be :precision
     */
    public function assertCurrencyPrecision($reference, $precision)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ((int) $currency->precision != (int) $precision) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" precision, but "%s" was expected.',
                $reference,
                $currency->precision,
                $precision
            ));
        }
    }

    /**
     * @Then currency :currencyReference should be available in shop :shopReference
     */
    public function assertCurrencyIsAvailableInShop($currencyReference, $shopReference)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($currencyReference);
        /** @var \Shop $shop */
        $shop = SharedStorage::getStorage()->get($shopReference);

        if (!in_array($shop->id, $currency->getAssociatedShops())) {
            throw new RuntimeException(sprintf(
                'Currency "%s" is not associated with "%s" shop',
                $currencyReference,
                $shopReference
            ));
        }
    }

    /**
     * @Given currency with :isoCode has been deleted
     */
    public function assertCurrencyHasBeenDeleted($isoCode)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('deleted = 1');
        $query->where('iso_code = \'' . pSQL($isoCode) . '\'');

        $currencyId = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" should be deleted in database', $isoCode));
        }
    }

    /**
     * @Given currency with :isoCode has been deactivated
     */
    public function assertCurrencyHasBeenDeactivated($isoCode)
    {
        $query = new DbQuery();
        $query->select('c.id_currency');
        $query->from('currency', 'c');
        $query->where('active = 0');
        $query->where('iso_code = \'' . pSQL($isoCode) . '\'');

        $currencyId = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query->build());

        if (!$currencyId) {
            throw new RuntimeException(sprintf('Currency with ISO Code "%s" should be deactivated in database', $isoCode));
        }
    }

    /**
     * @Then currency :reference numeric iso code should be :numericIsoCode
     */
    public function assertCurrencyNumericIsoCode($reference, $numericIsoCode)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ('valid' === $numericIsoCode) {
            if ((int) $currency->numeric_iso_code <= 0) {
                throw new RuntimeException(sprintf(
                    'Currency "%s" has invalid numeric iso code "%s".',
                    $reference,
                    $currency->numeric_iso_code
                ));
            }
        } elseif ((int) $currency->numeric_iso_code !== (int) $numericIsoCode) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" numeric iso code, but "%s" was expected.',
                $reference,
                $currency->numeric_iso_code,
                $numericIsoCode
            ));
        }
    }

    /**
     * @Then currency :reference name should be :name
     */
    public function assertCurrencyName($reference, $name)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ($currency->name !== $name) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" name, but "%s" was expected.',
                $reference,
                $currency->name,
                $name
            ));
        }
    }

    /**
     * @Then currency :reference symbol should be :symbol
     */
    public function assertCurrencySymbol($reference, $symbol)
    {
        /** @var Currency $currency */
        $currency = SharedStorage::getStorage()->get($reference);

        if ($currency->symbol !== $symbol) {
            throw new RuntimeException(sprintf(
                'Currency "%s" has "%s" symbol, but "%s" was expected.',
                $reference,
                $currency->symbol,
                $symbol
            ));
        }
    }
}
