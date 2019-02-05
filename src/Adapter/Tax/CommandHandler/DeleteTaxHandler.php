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

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\CannotDeleteTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxNotFoundException;
use Tax;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\DeleteTaxHandlerInterface;
use PrestaShopException;

/**
 * Handles command which deletes Tax using legacy object model
 */
final class DeleteTaxHandler implements DeleteTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteTaxCommand $command)
    {
        $taxId = $command->getTaxId()->getValue();
        $entity = new Tax($taxId);

        if ($taxId !== $entity->id) {
            throw new TaxNotFoundException(
                sprintf(
                    'Tax object with id "%s" has not been found for deletion.',
                    $taxId
                )
            );
        }

        try {
            if (false === $entity->delete()) {
                throw new CannotDeleteTaxException(
                    sprintf(
                        'Unable to delete Tax object with id "%s"',
                        $taxId
                    )
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotDeleteTaxException(
                sprintf(
                    'An error occurred when deleting Tax object with id "%s"',
                    $taxId
                ),
                0,
                $e
            );
        }
    }
}
