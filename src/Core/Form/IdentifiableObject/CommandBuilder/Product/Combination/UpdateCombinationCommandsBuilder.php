<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;

/**
 * This command builder builds the unified UpdateCombinationCommand which includes many sub scopes of the combination
 * edition, to clarify the configuration each sub-domain is configured separately but in the end we use one config, one
 * builder and one command for the whole Combination fields updates.
 */
class UpdateCombinationCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * @var string
     */
    private $modifyAllNamePrefix;

    /**
     * @param string $modifyAllNamePrefix
     */
    public function __construct(string $modifyAllNamePrefix)
    {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[header][is_default]', 'setIsDefault', DataField::TYPE_BOOL)
        ;

        $this
            ->configurePriceImpact($config)
            ->configureDetails($config)
            ->configureStock($config, $formData)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateCombinationCommand($combinationId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateCombinationCommand($combinationId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }

    private function configurePriceImpact(CommandBuilderConfig $config): self
    {
        $config
            ->addMultiShopField('[price_impact][price_tax_excluded]', 'setImpactOnPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[price_impact][ecotax_tax_excluded]', 'setEcoTax', DataField::TYPE_STRING)
            ->addMultiShopField('[price_impact][unit_price_tax_excluded]', 'setImpactOnUnitPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[price_impact][wholesale_price]', 'setWholesalePrice', DataField::TYPE_STRING)
            ->addMultiShopField('[price_impact][weight]', 'setImpactOnWeight', DataField::TYPE_STRING)
        ;

        return $this;
    }

    private function configureDetails(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[references][reference]', 'setReference', DataField::TYPE_STRING)
            ->addField('[references][mpn]', 'setMpn', DataField::TYPE_STRING)
            ->addField('[references][upc]', 'setUpc', DataField::TYPE_STRING)
            ->addField('[references][ean_13]', 'setGtin', DataField::TYPE_STRING)
            ->addField('[references][isbn]', 'setIsbn', DataField::TYPE_STRING)
        ;

        return $this;
    }

    private function configureStock(CommandBuilderConfig $config, array $formData): self
    {
        $config
            ->addMultiShopField('[stock][quantities][minimal_quantity]', 'setMinimalQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT)
            ->addMultiShopField('[stock][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
            ->addField('[stock][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
            ->addField('[stock][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
        ;

        $lowStockThresholdSwitchKey = sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX);

        if (
            // if low stock threshold switch is falsy, then we must set lowStockThreshold to its disabled value
            // which will end up being 0 after falsy bool to int conversion
            isset($formData['stock']['options'][$lowStockThresholdSwitchKey])
            && !$formData['stock']['options'][$lowStockThresholdSwitchKey]
        ) {
            $config->addMultiShopField(sprintf('[stock][options][%s]', $lowStockThresholdSwitchKey), 'setLowStockThreshold', DataField::TYPE_INT);
        } else {
            // else we simply set the low stock threshold value from the form
            $config->addMultiShopField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT);
        }

        return $this;
    }
}
