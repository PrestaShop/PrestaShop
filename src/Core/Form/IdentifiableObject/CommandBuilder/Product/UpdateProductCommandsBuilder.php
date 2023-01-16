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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds @see UpdateProductCommand for both single and All shops
 */
class UpdateProductCommandsBuilder implements MultiShopProductCommandsBuilderInterface
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
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $this
            ->configureBasicInformation($config)
            ->configureOptions($config, $formData)
            ->configurePrices($config)
            ->configureSeo($config)
            ->configureDetails($config)
            ->configureShipping($config)
            ->configureStockInformation($config, $formData)
        ;

        $config->addField('[header][active]', 'setActive', DataField::TYPE_BOOL);

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configureBasicInformation(CommandBuilderConfig $config): self
    {
        $config
            ->addMultiShopField('[header][name]', 'setLocalizedNames', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description]', 'setLocalizedDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description_short]', 'setLocalizedShortDescriptions', DataField::TYPE_ARRAY)
        ;

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configureOptions(CommandBuilderConfig $config, array $formData): self
    {
        $config
            ->addField('[description][manufacturer]', 'setManufacturerId', DataField::TYPE_INT)
            ->addMultiShopField('[options][visibility][online_only]', 'setOnlineOnly', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][visibility]', 'setVisibility', DataField::TYPE_STRING)
            ->addMultiShopField('[options][visibility][available_for_order]', 'setAvailableForOrder', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][show_price]', 'setShowPrice', DataField::TYPE_BOOL)
            ->addMultiShopField('[specifications][show_condition]', 'setShowCondition', DataField::TYPE_BOOL)
        ;

        // based on show_condition value, the condition field can be disabled, in that case "condition" won't exist in request
        // and will end up being "" in command if added into config without this if, which causes constraint error
        if (!empty($formData['specifications']['condition'])) {
            $config->addMultiShopField('[specifications][condition]', 'setCondition', DataField::TYPE_STRING);
        }

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configurePrices(CommandBuilderConfig $config): self
    {
        $config
            ->addMultiShopField('[pricing][retail_price][price_tax_excluded]', 'setPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[pricing][retail_price][ecotax_tax_excluded]', 'setEcotax', DataField::TYPE_STRING)
            ->addMultiShopField('[pricing][retail_price][tax_rules_group_id]', 'setTaxRulesGroupId', DataField::TYPE_INT)
            ->addMultiShopField('[pricing][on_sale]', 'setOnSale', DataField::TYPE_BOOL)
            ->addMultiShopField('[pricing][wholesale_price]', 'setWholesalePrice', DataField::TYPE_STRING)
            ->addMultiShopField('[pricing][unit_price][price_tax_excluded]', 'setUnitPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[pricing][unit_price][unity]', 'setUnity', DataField::TYPE_STRING)
        ;

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configureSeo(CommandBuilderConfig $config): self
    {
        $config
            ->addMultiShopField('[seo][meta_title]', 'setLocalizedMetaTitles', DataField::TYPE_ARRAY)
            ->addMultiShopField('[seo][meta_description]', 'setLocalizedMetaDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[seo][link_rewrite]', 'setLocalizedLinkRewrites', DataField::TYPE_ARRAY)
            ->addMultiShopCompoundField('setRedirectOption', [
                '[seo][redirect_option][type]' => DataField::TYPE_STRING,
                '[seo][redirect_option][target][id]' => [
                    'type' => DataField::TYPE_INT,
                    'default' => 0,
                ],
            ])
        ;

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configureDetails(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[specifications][references][reference]', 'setReference', DataField::TYPE_STRING)
            ->addField('[specifications][references][mpn]', 'setMpn', DataField::TYPE_STRING)
            ->addField('[specifications][references][upc]', 'setUpc', DataField::TYPE_STRING)
            ->addField('[specifications][references][ean_13]', 'setEan13', DataField::TYPE_STRING)
            ->addField('[specifications][references][isbn]', 'setIsbn', DataField::TYPE_STRING)
        ;

        return $this;
    }

    /**
     * @param CommandBuilderConfig $config
     *
     * @return self
     */
    private function configureShipping(CommandBuilderConfig $config): self
    {
        $config
            ->addField('[shipping][dimensions][width]', 'setWidth', DataField::TYPE_STRING)
            ->addField('[shipping][dimensions][height]', 'setHeight', DataField::TYPE_STRING)
            ->addField('[shipping][dimensions][depth]', 'setDepth', DataField::TYPE_STRING)
            ->addField('[shipping][dimensions][weight]', 'setWeight', DataField::TYPE_STRING)
            ->addField('[shipping][delivery_time_note_type]', 'setDeliveryTimeNoteType', DataField::TYPE_INT)
            ->addMultiShopField('[shipping][additional_shipping_cost]', 'setAdditionalShippingCost', DataField::TYPE_STRING)
            ->addMultiShopField('[shipping][delivery_time_notes][in_stock]', 'setLocalizedDeliveryTimeInStockNotes', DataField::TYPE_ARRAY)
            ->addMultiShopField('[shipping][delivery_time_notes][out_of_stock]', 'setLocalizedDeliveryTimeOutOfStockNotes', DataField::TYPE_ARRAY)
        ;

        return $this;
    }

    private function configureStockInformation(CommandBuilderConfig $config, array $formData): self
    {
        $config
            ->addMultiShopField('[stock][quantities][minimal_quantity]', 'setMinimalQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][disabling_switch_low_stock_threshold]', 'setLowStockAlert', DataField::TYPE_BOOL)
            ->addMultiShopField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT)
            ->addMultiShopField('[stock][pack_stock_type]', 'setPackStockType', DataField::TYPE_INT)
            ->addMultiShopField('[stock][availability][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
        ;

        $productType = $formData['header']['type'] ?? ProductType::TYPE_STANDARD;
        if ($productType === ProductType::TYPE_COMBINATIONS) {
            $config
                ->addMultiShopField('[combinations][availability][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
                ->addMultiShopField('[combinations][availability][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
            ;
        } else {
            $config
                ->addMultiShopField('[stock][availability][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
                ->addMultiShopField('[stock][availability][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
            ;
        }

        return $this;
    }
}
