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

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\DeleteTaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShopException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\DeleteTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\DeleteTaxHandlerInterface;

/**
 * Handles command which deletes Tax using legacy object model
 */
final class DeleteTaxHandler extends AbstractTaxHandler implements DeleteTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteTaxCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());
        $taxIdValue = $command->getTaxId()->getValue();

        try {
            if (!$tax->delete()) {
                throw new DeleteTaxException(
                    sprintf('Cannot delete Tax object with id "%s"', $taxIdValue),
                    DeleteTaxException::FAILED_DELETE
                );
            }
        } catch (PrestaShopException $e) {
            throw new TaxException(
                sprintf('An error occurred when deleting Tax object with id "%s"', $taxIdValue)
            );
        }
    }
}
