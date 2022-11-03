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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\BulkDeleteTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\BulkDeleteTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotBulkDeleteTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;

/**
 * Handles multiple tax rules group deletion
 */
final class BulkDeleteTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements BulkDeleteTaxRulesGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotBulkDeleteTaxRulesGroupException
     */
    public function handle(BulkDeleteTaxRulesGroupCommand $command): void
    {
        $errors = [];

        foreach ($command->getTaxRulesGroupIds() as $taxRulesGroupId) {
            try {
                $taxRulesGroup = $this->getTaxRulesGroup($taxRulesGroupId);

                if (!$this->deleteTaxRulesGroup($taxRulesGroup)) {
                    $errors[] = $taxRulesGroup->id;
                }
            } catch (TaxRulesGroupException $e) {
                $errors[] = $taxRulesGroupId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new CannotBulkDeleteTaxRulesGroupException($errors, 'Failed to delete all tax rules groups without errors');
        }
    }
}
