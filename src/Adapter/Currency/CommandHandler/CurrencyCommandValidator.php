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

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
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
     * @param EditCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     */
    public function assertDefaultCurrencyIsNotBeingDisabled(EditCurrencyCommand $command)
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
     * @param EditCurrencyCommand $command
     *
     * @throws DefaultCurrencyInMultiShopException
     */
    public function assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop(Currency $currency, EditCurrencyCommand $command)
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
