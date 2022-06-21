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

namespace PrestaShop\PrestaShop\Adapter\TaxRulesGroup\CommandHandler;

use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\AbstractTaxRulesGroupHandler;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Command\EditTaxRulesGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\CommandHandler\EditTaxRulesGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\CannotUpdateTaxRulesGroupException;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Exception\TaxRulesGroupException;
use PrestaShopException;

/**
 * Handles tax rules group edition
 */
class EditTaxRulesGroupHandler extends AbstractTaxRulesGroupHandler implements EditTaxRulesGroupHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CannotUpdateTaxRulesGroupException
     * @throws TaxRulesGroupException
     */
    public function handle(EditTaxRulesGroupCommand $command): void
    {
        $taxRulesGroup = $this->getTaxRulesGroup($command->getTaxRulesGroupId());

        if (null !== $command->getName()) {
            $taxRulesGroup->name = $command->getName();
        }
        if (null !== $command->isEnabled()) {
            $taxRulesGroup->active = $command->isEnabled();
        }

        try {
            if (false === $taxRulesGroup->validateFields(false)
                || false === $taxRulesGroup->validateFieldsLang(false)) {
                throw new TaxRulesGroupException('Tax Rules Group contains invalid field values');
            }
            if (false === $taxRulesGroup->update()) {
                throw new CannotUpdateTaxRulesGroupException(
                    sprintf(
                        'Failed to update cms page with id %s',
                        $command->getTaxRulesGroupId()->getValue()
                    )
                );
            }
            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($taxRulesGroup, $command->getShopAssociation());
            }

            /* @phpstan-ignore-next-line */
            if (!$taxRulesGroup->update()) {
                throw new TaxRulesGroupException(sprintf('Cannot update tax with id "%s"', $taxRulesGroup->id));
            }
        } catch (PrestaShopException $e) {
            throw new TaxRulesGroupException(sprintf('Cannot update tax with id "%s"', $taxRulesGroup->id));
        }
    }
}
