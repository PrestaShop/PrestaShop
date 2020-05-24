<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\BulkDeleteCurrenciesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\BulkDeleteCurrenciesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
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
     * @throws CurrencyException
     */
    public function handle(BulkDeleteCurrenciesCommand $command)
    {
        foreach ($command->getCurrencyIds() as $currencyId) {
            $entity = new Currency($currencyId->getValue());

            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" has not been found for deletion.', $currencyId->getValue()));
            }

            $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabled($currencyId->getValue(), $this->defaultCurrencyId);
            $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromAnyShop($entity);

            try {
                if (false === $entity->delete()) {
                    throw new CannotDeleteCurrencyException(sprintf('Unable to delete currency object with id "%s"', $currencyId->getValue()));
                }
            } catch (PrestaShopException $e) {
                throw new CurrencyException(sprintf('An error occurred when  deleting Currency object with id "%s"', $currencyId->getValue()), 0, $e);
            }
        }
    }
}
