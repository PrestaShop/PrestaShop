<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
