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

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds commands from product stock form type
 */
final class StockCommandsBuilder implements MultiShopProductCommandsBuilderInterface
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
        if (!isset($formData['stock']) && !isset($formData['combinations'])) {
            return [];
        }

        $productType = $formData['header']['type'] ?? ProductType::TYPE_STANDARD;
        if ($productType === ProductType::TYPE_COMBINATIONS) {
            return $this->buildCommandsForProductWithCombinations($productId, $formData, $singleShopConstraint);
        }

        return $this->buildCommandsForRegularProduct($productId, $formData, $singleShopConstraint);
    }

    private function buildCommandsForRegularProduct(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[stock][quantities][delta_quantity][delta]', 'setDeltaQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][quantities][minimal_quantity]', 'setMinimalQuantity', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][stock_location]', 'setLocation', DataField::TYPE_STRING)
            ->addMultiShopField('[stock][options][low_stock_threshold]', 'setLowStockThreshold', DataField::TYPE_INT)
            ->addMultiShopField('[stock][options][disabling_switch_low_stock_threshold]', 'setLowStockAlert', DataField::TYPE_BOOL)
            ->addMultiShopField('[stock][pack_stock_type]', 'setPackStockType', DataField::TYPE_INT)
            ->addMultiShopField('[stock][availability][out_of_stock_type]', 'setOutOfStockType', DataField::TYPE_INT)
            ->addMultiShopField('[stock][availability][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
            ->addMultiShopField('[stock][availability][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
            ->addMultiShopField('[stock][availability][available_date]', 'setAvailableDate', DataField::TYPE_DATETIME)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductStockInformationCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductStockInformationCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }

    private function buildCommandsForProductWithCombinations(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[combinations][availability][out_of_stock_type]', 'setOutOfStockType', DataField::TYPE_INT)
            ->addMultiShopField('[combinations][availability][available_now_label]', 'setLocalizedAvailableNowLabels', DataField::TYPE_ARRAY)
            ->addMultiShopField('[combinations][availability][available_later_label]', 'setLocalizedAvailableLaterLabels', DataField::TYPE_ARRAY)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductStockInformationCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductStockInformationCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }
}
