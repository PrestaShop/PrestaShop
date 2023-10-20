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
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\ToggleCurrencyStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotToggleCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShopException;
use Shop;

/**
 * Class ToggleCurrencyStatusHandler is responsible for toggling currency status.
 *
 * @internal
 */
final class ToggleCurrencyStatusHandler implements ToggleCurrencyStatusHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param int $defaultCurrencyId
     */
    public function __construct($defaultCurrencyId)
    {
        $this->defaultCurrencyId = (int) $defaultCurrencyId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(ToggleCurrencyStatusCommand $command)
    {
        $entity = new Currency($command->getCurrencyId()->getValue());

        if (0 >= $entity->id) {
            throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" has not been found for toggling.', $command->getCurrencyId()->getValue()));
        }

        if ($entity->active) {
            $this->assertDefaultCurrencyIsNotBeingDisabled($entity);
            $this->assertDefaultCurrencyIsNotBeingDisabledFromAnyShop($entity);
        }

        try {
            if (false === $entity->toggleStatus()) {
                throw new CannotToggleCurrencyException(sprintf('Unable to toggle Currency with id "%s"', $command->getCurrencyId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new CurrencyException(sprintf('An error occurred when toggling status for Currency object with id "%s"', $command->getCurrencyId()->getValue()), 0, $e);
        }
    }

    /**
     * @param Currency $currency
     *
     * @throws CannotDisableDefaultCurrencyException
     */
    private function assertDefaultCurrencyIsNotBeingDisabled(Currency $currency)
    {
        if ((int) $currency->id === $this->defaultCurrencyId) {
            throw new CannotDisableDefaultCurrencyException(sprintf('Currency with id "%s" is the default currency and cannot be disabled.', $currency->id));
        }
    }

    /**
     * @param Currency $currency
     *
     * @throws DefaultCurrencyInMultiShopException
     */
    private function assertDefaultCurrencyIsNotBeingDisabledFromAnyShop(Currency $currency)
    {
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

            $shop = new Shop($shopId);
            throw new DefaultCurrencyInMultiShopException($currency->name, $shop->name, sprintf('Currency with id %s cannot be disabled from shop with id %s because its the default currency.', $currency->id, $shopId), DefaultCurrencyInMultiShopException::CANNOT_DISABLE_CURRENCY);
        }
    }
}
