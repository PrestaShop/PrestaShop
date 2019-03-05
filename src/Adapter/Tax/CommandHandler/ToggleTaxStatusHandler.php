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
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\ToggleTaxStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\ToggleTaxStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\UpdateTaxException;
use PrestaShopException;
use Tax;

/**
 * Handles command which toggles Tax status using legacy object model
 */
final class ToggleTaxStatusHandler extends AbstractTaxHandler implements ToggleTaxStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(ToggleTaxStatusCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());
        $taxIdValue = $command->getTaxId()->getValue();
        $tax->active = $command->getExpectedStatus();

        try {
            if (!$tax->save()) {
                throw new UpdateTaxException(
                    sprintf('Unable to toggle Tax with id "%s"', $taxIdValue),
                    UpdateTaxException::FAILED_UPDATE_STATUS
                );
            }
        } catch (PrestaShopException $e) {
            throw new TaxException(
                sprintf('An error occurred when enabling Tax with id "%s"', $taxIdValue)
            );
        }
    }
}
