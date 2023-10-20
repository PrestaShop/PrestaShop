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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkDeleteCurrenciesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\BulkDeleteCurrenciesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\BulkDeleteCurrenciesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShopException;

/**
 * Deletes currencies using legacy currency object model
 *
 * @internal
 */
final class BulkDeleteCurrenciesHandler extends AbstractCurrencyHandler implements BulkDeleteCurrenciesHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @param int $defaultCurrencyId
     */
    public function __construct(int $defaultCurrencyId)
    {
        $this->defaultCurrencyId = (int) $defaultCurrencyId;
    }

    /**
     * @param BulkDeleteCurrenciesCommand $command
     *
     * @throws BulkDeleteCurrenciesException
     */
    public function handle(BulkDeleteCurrenciesCommand $command)
    {
        $faileds = [];

        foreach ($command->getCurrencyIds() as $currencyId) {
            $entity = new Currency($currencyId->getValue());

            if (0 >= $entity->id) {
                $faileds[] = $currencyId->getValue();
                continue;
            }

            try {
                $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabled($currencyId->getValue(), $this->defaultCurrencyId);
                $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop($entity);
            } catch (CurrencyException $e) {
                $faileds[] = $currencyId->getValue();
                continue;
            }

            try {
                if (false === $entity->delete()) {
                    $faileds[] = $currencyId->getValue();
                }
            } catch (PrestaShopException $e) {
                $faileds[] = $currencyId->getValue();
            }
        }

        if (!empty($faileds)) {
            throw new BulkDeleteCurrenciesException($faileds, 'Failed to delete all of selected currencies');
        }
    }
}
