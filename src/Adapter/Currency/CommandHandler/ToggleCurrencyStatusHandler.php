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
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\ToggleCurrencyStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotToggleCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShopException;

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
            throw new CurrencyNotFoundException(
                sprintf(
                    'Currency object with id "%s" has not been found for toggling.',
                    $command->getCurrencyId()->getValue()
                )
            );
        }

        if ($entity->active && $command->getCurrencyId()->getValue() === $this->defaultCurrencyId) {
            throw new CannotDisableDefaultCurrencyException(
                sprintf(
                    'Currency with id "%s" is the default currency and cannot be disabled.',
                    $command->getCurrencyId()->getValue()
                )
            );
        }

        try {
            if (false === $entity->toggleStatus()) {
                throw new CannotToggleCurrencyException(
                    sprintf(
                        'Unable to toggle Currency with id "%s"',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $e) {
            throw new CurrencyException(
                sprintf(
                    'An error occurred when toggling status for Currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                ),
                0,
                $e
            );
        }
    }
}
