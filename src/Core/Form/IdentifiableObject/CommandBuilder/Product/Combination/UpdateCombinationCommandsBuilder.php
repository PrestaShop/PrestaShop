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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * This command builder builds the unified UpdateCombinationCommand which includes many sub scopes of the combination
 * edition, to clarify the configuration each sub-domain is configured separately but in the end we use one config, one
 * builder and one command for the whole Combination fields updates.
 */
class UpdateCombinationCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData): array
    {
        $config = new CommandBuilderConfig();
        $this
            ->configurePriceImpact($config)
            ->configureDetails($config)
            ->configureStock($config)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateCombinationCommand($combinationId->getValue());

        return $commandBuilder->buildCommands($formData, $shopCommand);
    }

    private function configurePriceImpact(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[price_impact][price_tax_excluded]', 'setImpactOnPrice', DataField::TYPE_STRING)
            ->addField('[price_impact][ecotax_tax_excluded]', 'setEcoTax', DataField::TYPE_STRING)
            ->addField('[price_impact][unit_price_tax_excluded]', 'setImpactOnUnitPrice', DataField::TYPE_STRING)
            ->addField('[price_impact][wholesale_price]', 'setWholesalePrice', DataField::TYPE_STRING)
            ->addField('[price_impact][weight]', 'setImpactOnWeight', DataField::TYPE_STRING)
        ;

        return $this;
    }

    private function configureDetails(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[references][reference]', 'setReference', DataField::TYPE_STRING)
            ->addField('[references][mpn]', 'setMpn', DataField::TYPE_STRING)
            ->addField('[references][upc]', 'setUpc', DataField::TYPE_STRING)
            ->addField('[references][ean_13]', 'setEan13', DataField::TYPE_STRING)
            ->addField('[references][isbn]', 'setIsbn', DataField::TYPE_STRING)
        ;

        return $this;
    }

    private function configureStock(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[stock][quantities][minimal_quantity]', 'setMinimalQuantity', DataField::TYPE_INT)
            ->addField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT)
            ->addField('[stock][options][disabling_switch_low_stock_threshold]', 'setLowStockAlert', DataField::TYPE_BOOL)
            ->addField('[stock][pack_stock_type]', 'setPackStockType', DataField::TYPE_INT)
            ->addField('[stock][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
            ->addField('[stock][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
            ->addField('[stock][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
        ;

        return $this;
    }
}
