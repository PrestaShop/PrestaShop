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
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;

/**
 * Builds @see UpdateProductCommand for both single and All shops
 */
class UpdateProductCommandsBuilder implements ProductCommandsBuilderInterface
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

        $config->addMultiShopField('[header][active]', 'setActive', DataField::TYPE_BOOL);

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductCommand($productId->getValue(), ShopConstraint::allShops());

        $this->setNameDependingOnStatus($formData, $shopCommand);

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
            ->addMultiShopField('[details][show_condition]', 'setShowCondition', DataField::TYPE_BOOL)
        ;

        // based on show_condition value, the condition field can be disabled, in that case "condition" won't exist in request
        // and will end up being "" in command if added into config without this if, which causes constraint error
        if (!empty($formData['details']['condition'])) {
            $config->addMultiShopField('[details][condition]', 'setCondition', DataField::TYPE_STRING);
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
            ->addField('[details][references][reference]', 'setReference', DataField::TYPE_STRING)
            ->addField('[details][references][mpn]', 'setMpn', DataField::TYPE_STRING)
            ->addField('[details][references][upc]', 'setUpc', DataField::TYPE_STRING)
            ->addField('[details][references][ean_13]', 'setEan13', DataField::TYPE_STRING)
            ->addField('[details][references][isbn]', 'setIsbn', DataField::TYPE_STRING)
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
            ->addMultiShopField('[stock][pack_stock_type]', 'setPackStockType', DataField::TYPE_INT)
            ->addMultiShopField('[stock][availability][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
        ;

        $lowStockThresholdSwitchKey = sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX);

        if (
            // if low stock threshold switch is falsy, then we must set lowStockThreshold to its disabled value
            // which will end up being 0 after falsy bool to int conversion
            isset($formData['stock']['options'][$lowStockThresholdSwitchKey]) &&
            !$formData['stock']['options'][$lowStockThresholdSwitchKey]
        ) {
            $config->addMultiShopField(sprintf('[stock][options][%s]', $lowStockThresholdSwitchKey), 'setLowStockThreshold', DataField::TYPE_INT);
        } else {
            // else we simply set the low stock threshold value from the form
            $config->addMultiShopField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT);
        }

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

    /**
     * Name and status are related - when name is not filled, then product cannot be enabled.
     * When name is being updated for all shops, but status only for single shop, then status would be filled into
     * single shop command while name only in all shops command. Since single shop command is always executed first,
     * it will try to enable product before name is inserted in allShopsCommand and will end up throwing
     * error about name being empty.
     *
     * So to solve that, we check if status is being updated for single shop,
     * and if that is the case, then we manually fill the name into single shop command too.
     * So at the end it will end up updating name twice - with single shop command and with all shops command,
     * but at least it won't throw the error.
     *
     * @param array<string, mixed> $formData
     * @param UpdateProductCommand $singleShopCommand
     *
     * @return void
     */
    private function setNameDependingOnStatus(array $formData, UpdateProductCommand $singleShopCommand): void
    {
        if (
            !empty($formData['header']['active']) &&
            empty($formData['header'][$this->modifyAllNamePrefix . 'active']) &&
            isset($formData['header']['name'])
        ) {
            $singleShopCommand->setLocalizedNames($formData['header']['name']);
        }
    }
}
