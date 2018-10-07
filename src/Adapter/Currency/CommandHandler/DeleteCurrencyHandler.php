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
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\DeleteCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShopException;

/**
 * Class DeleteCurrencyHandler is responsible for handling the deletion of currency logic.
 */
final class DeleteCurrencyHandler implements DeleteCurrencyHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     *
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
    public function handle(DeleteCurrencyCommand $command)
    {
        $entity = new Currency($command->getCurrencyId()->getValue());

        if (0 >= $entity->id) {
            throw new CurrencyNotFoundException(
                sprintf(
                    'Currency object with id "%s" has not been found for deletion.',
                    $command->getCurrencyId()->getValue()
                )
            );
        }

        if ($command->getCurrencyId()->getValue() === $this->defaultCurrencyId) {
            throw new CannotDeleteDefaultCurrencyException(
                sprintf(
                    'Currency with id "%s" is the default currency and cannot be deleted.',
                    $command->getCurrencyId()->getValue()
                )
            );
        }

        try {
            if (false === $entity->delete()) {
                throw new CannotDeleteCurrencyException(
                    sprintf(
                        'Unable to delete currency object with id "%s"',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $e) {
            throw new CurrencyException(
                sprintf(
                    'An error occurred when  deleting Currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                ),
                0,
                $e
            );
        }
    }
}
