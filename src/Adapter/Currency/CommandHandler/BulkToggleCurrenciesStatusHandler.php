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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkToggleCurrenciesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\BulkToggleCurrenciesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\BulkToggleCurrenciesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShopException;

/**
 * Toggles multiple currencies status using legacy Currency object model
 *
 * @internal
 */
final class BulkToggleCurrenciesStatusHandler extends AbstractCurrencyHandler implements BulkToggleCurrenciesStatusHandlerInterface
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
     * @param BulkToggleCurrenciesStatusCommand $command
     *
     * @throws BulkToggleCurrenciesException
     */
    public function handle(BulkToggleCurrenciesStatusCommand $command)
    {
        $faileds = [];

        foreach ($command->getCurrencyIds() as $currency) {
            $entity = new Currency((int) $currency->getValue());

            if ($command->getStatus() == $entity->active) {
                continue;
            }

            if (0 >= $entity->id) {
                $faileds[] = $currency->getValue();
                continue;
            }

            if ($entity->active) {
                try {
                    $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabled($currency->getValue(), $this->defaultCurrencyId);
                    $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop($entity);
                } catch (CurrencyException $e) {
                    $faileds[] = $currency->getValue();
                    continue;
                }
            }

            try {
                if (false === $entity->toggleStatus()) {
                    $faileds[] = $currency->getValue();
                }
            } catch (PrestaShopException $e) {
                $faileds[] = $currency->getValue();
            }
        }

        if (!empty($faileds)) {
            throw new BulkToggleCurrenciesException($faileds, 'Failed to delete all of selected currencies');
        }
    }
}
