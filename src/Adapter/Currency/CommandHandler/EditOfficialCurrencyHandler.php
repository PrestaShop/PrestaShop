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

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Class EditOfficialCurrencyHandler is responsible for updating currencies.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditOfficialCurrencyHandler extends AbstractCurrencyHandler implements EditCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotDisableDefaultCurrencyException
     * @throws CannotUpdateCurrencyException
     * @throws CurrencyException
     * @throws CurrencyNotFoundException
     * @throws DefaultCurrencyInMultiShopException
     * @throws LocalizationException
     * @throws LanguageNotFoundException
     */
    public function handle(EditCurrencyCommand $command)
    {
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());
            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" was not found for currency update', $command->getCurrencyId()->getValue()));
            }
            $this->verify($entity, $command);
            $this->updateEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(sprintf('An error occurred when updating currency object with id "%s"', $command->getCurrencyId()->getValue()), 0, $exception);
        }
    }

    /**
     * @param Currency $entity
     * @param EditCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     * @throws DefaultCurrencyInMultiShopException
     */
    private function verify(Currency $entity, EditCurrencyCommand $command)
    {
        $this->validator->assertDefaultCurrencyIsNotBeingDisabled($command);
        $this->validator->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop($entity, $command);
    }
}
