<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use Shop;

/**
 * Validates that modifications managed via currency commands are valid and respect this domain
 * specific rules (avoid duplicate currencies, remove default currency, ...).
 */
final class CurrencyCommandValidator
{
    /**
     * @var LocaleRepository
     */
    private $localeRepoCLDR;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param CurrencyDataProviderInterface $currencyDataProvider
     * @param int $defaultCurrencyId
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        CurrencyDataProviderInterface $currencyDataProvider,
        int $defaultCurrencyId
    ) {
        $this->localeRepoCLDR = $localeRepoCLDR;
        $this->currencyDataProvider = $currencyDataProvider;
        $this->defaultCurrencyId = $defaultCurrencyId;
    }

    /**
     * @param string $isoCode
     *
     * @throws InvalidUnofficialCurrencyException
     */
    public function assertCurrencyIsNotInReference(string $isoCode)
    {
        /*
         * Every locale has the same list of currencies (even those defined in only one language) so it
         * doesn't matter which one is used to perform this check.
         */
        $locale = $this->localeRepoCLDR->getLocale('en');
        $cldrCurrency = $locale->getCurrency($isoCode);
        if (null !== $cldrCurrency) {
            throw new InvalidUnofficialCurrencyException(sprintf('Unofficial currency with iso code "%s" is invalid because it matches a currency from CLDR database', $isoCode), $isoCode);
        }
    }

    /**
     * Throws an error if currency is available in the database (soft deleted currencies don't count)
     *
     * @param string $isoCode
     *
     * @throws CurrencyConstraintException
     */
    public function assertCurrencyIsNotAvailableInDatabase(string $isoCode)
    {
        $currency = $this->currencyDataProvider->getCurrencyByIsoCode($isoCode);

        if (null !== $currency && !$currency->deleted) {
            throw new CurrencyConstraintException(sprintf('Currency with iso code "%s" already exists and cannot be created', $isoCode), CurrencyConstraintException::CURRENCY_ALREADY_EXISTS);
        }
    }

    /**
     * Prevents from default currency being disabled.
     *
     * @param EditCurrencyCommand|EditUnofficialCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     */
    public function assertDefaultCurrencyIsNotBeingDisabled(EditCurrencyCommand|EditUnofficialCurrencyCommand $command)
    {
        if (!$command->isEnabled() && $command->getCurrencyId()->getValue() === $this->defaultCurrencyId) {
            throw new CannotDisableDefaultCurrencyException(sprintf('Currency with id "%s" is the default currency and cannot be disabled.', $command->getCurrencyId()->getValue()));
        }
    }

    /**
     * On each shop there might be different default currency. This function prevents from removing shop association
     * from each shop and checks that the shop is not being disabled as well.
     *
     * @param Currency $currency
     * @param EditCurrencyCommand|EditUnofficialCurrencyCommand $command
     *
     * @throws DefaultCurrencyInMultiShopException
     */
    public function assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop(Currency $currency, EditCurrencyCommand|EditUnofficialCurrencyCommand $command)
    {
        if (empty($command->getShopIds())) {
            return;
        }
        $shopIds = $command->getShopIds();
        $allShopIds = Shop::getShops(false, null, true);

        foreach ($allShopIds as $shopId) {
            $shopDefaultCurrencyId = (int) Configuration::get(
                'PS_CURRENCY_DEFAULT',
                null,
                null,
                $shopId
            );

            if ((int) $currency->id !== $shopDefaultCurrencyId) {
                continue;
            }

            if (!in_array($shopId, $shopIds)) {
                $shop = new Shop($shopId);
                throw new DefaultCurrencyInMultiShopException($currency->getName(), $shop->name, sprintf('Currency with id %s cannot be unassigned from shop with id %s because its the default currency.', $currency->id, $shopId), DefaultCurrencyInMultiShopException::CANNOT_REMOVE_CURRENCY);
            }

            if (!$command->isEnabled()) {
                $shop = new Shop($shopId);
                throw new DefaultCurrencyInMultiShopException($currency->getName(), $shop->name, sprintf('Currency with id %s cannot be disabled from shop with id %s because its the default currency.', $currency->id, $shopId), DefaultCurrencyInMultiShopException::CANNOT_DISABLE_CURRENCY);
            }
        }
    }
}
