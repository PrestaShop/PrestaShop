<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\UpdateCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\UpdateCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShopException;

/**
 * Class UpdateCurrencyHandler
 */
final class UpdateCurrencyHandler implements UpdateCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(UpdateCurrencyCommand $command)
    {
        //todo: shop assocs
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());

            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(
                    sprintf(
                        'Currency object with id "%s" was not found for currency update',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }

            $entity->iso_code = $command->getIsoCode();
            $entity->active = $command->isEnabled();
            $entity->conversion_rate = $command->getExchangeRate();

            if (false === $entity->update()) {
                throw new CannotUpdateCurrencyException(
                    sprintf(
                        'An error occured when updating currency object with id "%s"',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(
                sprintf(
                    'An error occured when updating currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                ),
                0,
                $exception
            );
        }
    }
}
