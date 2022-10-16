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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\BasicInformationInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductDetailsInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductOptionsInput;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductInput\ProductPricesInput;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

//@todo: abit confusing when it should always build only one command but is called plural and returns array. smth needs adjustments
class UpdateProductCommandsBuilder implements MultiShopProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $shopConstraint): array
    {
        $updateProductCommand = new UpdateProductCommand($productId->getValue(), $shopConstraint);

        $updateProductCommand
            ->setBasicInformation($this->buildBasicInfo($formData))
            ->setOptions($this->buildOptions($formData))
            ->setPrices($this->buildPrices($formData))
            ->setDetails($this->buildDetails($formData))
        ;

        return [$updateProductCommand];
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return BasicInformationInput|null
     */
    private function buildBasicInfo(array $formData): ?BasicInformationInput
    {
        if (empty($formData['description']) && empty($formData['header']['name'])) {
            return null;
        }

        $config = new CommandBuilderConfig();
        $config
            ->addMultiShopField('[header][name]', 'setLocalizedNames', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description]', 'setLocalizedDescriptions', DataField::TYPE_ARRAY)
            ->addMultiShopField('[description][description_short]', 'setLocalizedShortDescriptions', DataField::TYPE_ARRAY)
        ;

        $commandBuilder = new CommandBuilder($config);
        $input = new BasicInformationInput();
        $inputs = $commandBuilder->buildCommands($formData, $input);

        return $inputs[0] ?? null;
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return ProductOptionsInput|null
     */
    private function buildOptions(array $formData): ?ProductOptionsInput
    {
        if (empty($formData['options']) &&
            !isset($formData['description']['manufacturer']) &&
            !isset($formData['specifications'])) {
            return null;
        }

        $config = new CommandBuilderConfig();
        $config
            ->addField('[description][manufacturer]', 'setManufacturerId', DataField::TYPE_INT)
            ->addMultiShopField('[options][visibility][online_only]', 'setOnlineOnly', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][visibility]', 'setVisibility', DataField::TYPE_STRING)
            ->addMultiShopField('[options][visibility][available_for_order]', 'setAvailableForOrder', DataField::TYPE_BOOL)
            ->addMultiShopField('[options][visibility][show_price]', 'setShowPrice', DataField::TYPE_BOOL)
            ->addMultiShopField('[specifications][show_condition]', 'setShowCondition', DataField::TYPE_BOOL)
        ;

        // based on show_condition value, the condition field can be disabled, in that case "condition" won't exist in request
        if (!empty($formData['specifications']['condition'])) {
            $config->addMultiShopField('[specifications][condition]', 'setCondition', DataField::TYPE_STRING);
        }

        $commandBuilder = new CommandBuilder($config);
        $input = new ProductOptionsInput();
        $inputs = $commandBuilder->buildCommands($formData, $input);

        return $inputs[0] ?? null;
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return ProductPricesInput|null
     */
    private function buildPrices(array $formData): ?ProductPricesInput
    {
        // using commandBuilder handles the "isset" checks and some other repetitive checks.
        $priceData = $formData['pricing'];
        $config = new CommandBuilderConfig();
        $config
            ->addMultiShopField('[retail_price][price_tax_excluded]', 'setPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[retail_price][ecotax_tax_excluded]', 'setEcotax', DataField::TYPE_STRING)
            ->addMultiShopField('[retail_price][tax_rules_group_id]', 'setTaxRulesGroupId', DataField::TYPE_INT)
            ->addMultiShopField('[on_sale]', 'setOnSale', DataField::TYPE_BOOL)
            ->addMultiShopField('[wholesale_price]', 'setWholesalePrice', DataField::TYPE_STRING)
            ->addMultiShopField('[unit_price][price_tax_excluded]', 'setUnitPrice', DataField::TYPE_STRING)
            ->addMultiShopField('[unit_price][unity]', 'setUnity', DataField::TYPE_STRING)
        ;

        $commandBuilder = new CommandBuilder($config);
        $productPricesInput = new ProductPricesInput();

        $inputs = $commandBuilder->buildCommands($priceData, $productPricesInput);

        return $inputs[0] ?? null;
    }

    /**
     * @param array<string, mixed> $formData
     *
     * @return ProductDetailsInput|null
     */
    private function buildDetails(array $formData): ?ProductDetailsInput
    {
        if (empty($formData['specifications']['references'])) {
            return null;
        }

        $referencesData = $formData['specifications']['references'];
        $config = new CommandBuilderConfig();
        $config
            ->addField('[reference]', 'setReference', DataField::TYPE_STRING)
            ->addField('[mpn]', 'setMpn', DataField::TYPE_STRING)
            ->addField('[upc]', 'setUpc', DataField::TYPE_STRING)
            ->addField('[ean_13]', 'setEan13', DataField::TYPE_STRING)
            ->addField('[isbn]', 'setIsbn', DataField::TYPE_STRING)
        ;

        $commandBuilder = new CommandBuilder($config);
        $productDetailsInput = new ProductDetailsInput();

        $inputs = $commandBuilder->buildCommands($referencesData, $productDetailsInput);

        return $inputs[0] ?? null;
    }
}
