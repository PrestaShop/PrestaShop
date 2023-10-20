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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\CommandBuilderConfig;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\DataField;

/**
 * Builds following command for single and all shops:
 *
 * @see UpdateProductStockAvailableCommand
 */
class ProductStockAvailableCommandsBuilder implements ProductCommandsBuilderInterface
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
     * @param ProductId $productId
     * @param array $formData
     * @param ShopConstraint $singleShopConstraint
     *
     * @return UpdateProductStockAvailableCommand[]
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
            ->addMultiShopField('[stock][options][stock_location]', 'setLocation', DataField::TYPE_STRING)
            ->addMultiShopField('[stock][availability][out_of_stock_type]', 'setOutOfStockType', DataField::TYPE_INT)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductStockAvailableCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductStockAvailableCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }

    /**
     * For product with combinations we only handle one field out_of_stock_type which is common to all combinations,
     * the delta stock and location are handled combination by combination in another dedicated command
     *
     * @param ProductId $productId
     * @param array<string, mixed> $formData
     * @param ShopConstraint $singleShopConstraint
     *
     * @return UpdateProductStockAvailableCommand[]
     */
    private function buildCommandsForProductWithCombinations(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        $config = new CommandBuilderConfig($this->modifyAllNamePrefix);
        $config
            ->addMultiShopField('[combinations][availability][out_of_stock_type]', 'setOutOfStockType', DataField::TYPE_INT)
        ;

        $commandBuilder = new CommandBuilder($config);
        $shopCommand = new UpdateProductStockAvailableCommand($productId->getValue(), $singleShopConstraint);
        $allShopsCommand = new UpdateProductStockAvailableCommand($productId->getValue(), ShopConstraint::allShops());

        return $commandBuilder->buildCommands($formData, $shopCommand, $allShopsCommand);
    }
}
