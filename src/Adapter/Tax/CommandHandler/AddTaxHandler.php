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

namespace PrestaShop\PrestaShop\Adapter\Tax\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Tax\AbstractTaxHandler;
use PrestaShop\PrestaShop\Core\Domain\Tax\Command\AddTaxCommand;
use PrestaShop\PrestaShop\Core\Domain\Tax\CommandHandler\AddTaxHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Tax\Exception\TaxException;
use PrestaShop\PrestaShop\Core\Domain\Tax\ValueObject\TaxId;
use PrestaShopException;
use Tax;

/**
 * Handles command which is responsible for tax editing
 */
final class AddTaxHandler extends AbstractTaxHandler implements AddTaxHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws TaxException
     */
    public function handle(AddTaxCommand $command)
    {
        $tax = new Tax();

        $tax->name = $command->getLocalizedNames();
        $tax->rate = $command->getRate();
        $tax->active = $command->isEnabled();

        try {
            if (false === $tax->validateFields(false) || false === $tax->validateFieldsLang(false)) {
                throw new TaxException('Tax contains invalid field values');
            }

            if (!$tax->save()) {
                throw new TaxException(sprintf('Cannot create tax with id "%s"', $tax->id));
            }
        } catch (PrestaShopException $e) {
            throw new TaxException(sprintf('Cannot create tax with id "%s"', $tax->id));
        }

        return new TaxId((int) $tax->id);
    }
}
