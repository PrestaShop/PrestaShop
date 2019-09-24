<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\AddTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\AddTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotAddTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShopException;
use TaxRulesGroup;

/**
 * Handles addition of new tax rules group
 */
final class AddTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements AddTaxRulesGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotAddTaxRulesGroupException
     * @throws TaxRulesGroupException
     * @throws TaxRulesGroupConstraintException
     */
    public function handle(AddTaxRulesGroupCommand $command): TaxRulesGroupId
    {
        try {
            $taxRulesGroup = new TaxRulesGroup();

            $taxRulesGroup->name = $command->getName();
            $taxRulesGroup->active = $command->isEnabled();

            if (null !== $command->getShopAssociation()) {
                $taxRulesGroup->id_shop_list = $command->getShopAssociation();
            }

            $this->validateTaxRulesGroupFields($taxRulesGroup);

            if (false === $taxRulesGroup->add()) {
                throw new CannotAddTaxRulesGroupException('Failed to add tax rules group');
            }
        } catch (PrestaShopException $e) {
            throw new TaxRulesGroupException('An unexpected error occurred when adding tax rules group', 0, $e);
        }

        return new TaxRulesGroupId($taxRulesGroup->id);
    }
}
