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
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\EditTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\EditTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShopException;
use Tax;

/**
 * Handles command which is responsible for tax editing
 */
final class EditTaxHandler extends AbstractTaxHandler implements EditTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TaxException
     */
    public function handle(EditTaxCommand $command)
    {
        $tax = $this->getTax($command->getTaxId());

        if (null !== $command->getLocalizedNames()) {
            $tax->name = $command->getLocalizedNames();
        }
        if (null !== $command->getRate()) {
            $tax->rate = $command->getRate();
        }
        if (null !== $command->isEnabled()) {
            $tax->active = $command->isEnabled();
        }

        try {
            if (!$tax->update()) {
                throw new TaxException(
                    sprintf('Cannot update tax with id "%s"', $tax->id)
                );
            }
        } catch (PrestaShopException $e) {
            throw new TaxException(
                sprintf('Cannot update tax with id "%s"', $tax->id)
            );
        }
    }
}
