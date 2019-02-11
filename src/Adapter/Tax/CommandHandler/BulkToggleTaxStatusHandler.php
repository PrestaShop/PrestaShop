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

use PrestaShop\PrestaShop\Core\Domain\Tax\Command\BulkToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\BulkToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\UpdateTaxException;
use PrestaShopException;
use Tax;

/**
 * Handles command which toggles taxes status on bulk action using legacy object model
 */
final class BulkToggleTaxStatusHandler extends AbstractTaxHandler implements BulkToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkToggleTaxStatusCommand $command)
    {
        foreach ($command->getTaxIds() as $taxId) {
            $tax = new Tax($taxId->getValue());
            $this->assertTaxWasFound($taxId, $tax);
            $tax->active = $command->getStatus()->isEnabled();

            try {
                if (!$tax->save()) {
                    throw new UpdateTaxException(
                        sprintf('Unable to toggle Tax with id "%s"', $taxId->getValue()),
                        UpdateTaxException::FAILED_BULK_UPDATE_STATUS
                    );
                }
            } catch (PrestaShopException $e) {
                throw new TaxException(
                    sprintf('An error occurred when updating Tax status with id "%s"', $taxId->getValue())
                );
            }
        }
    }
}
