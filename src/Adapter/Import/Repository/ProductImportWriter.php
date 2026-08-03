<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Import\Repository;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Product\Validate\ProductValidator;
use PrestaShop\PrestaShop\Adapter\TaxRulesGroup\Repository\TaxRulesGroupRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotAddProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Import\Engine\Repository\ProductImportWriterInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use Product;

/**
 * Import fallback writer. Forced-id creation mirrors ProductRepository::create()
 * with ObjectModel::$force_id enabled (same pattern as combination creation),
 * writing the minimal product tables: product, product_shop, product_lang,
 * stock_available (via Product::add()) and category_product.
 *
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class ProductImportWriter extends AbstractMultiShopObjectModelRepository implements ProductImportWriterInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly ProductValidator $productValidator,
        private readonly CategoryRepository $categoryRepository,
        private readonly TaxRulesGroupRepository $taxRulesGroupRepository,
        private readonly Tools $tools,
    ) {
    }

    public function createProductWithId(int $forcedProductId, string $productType, int $shopId, array $localizedNames): void
    {
        $shopIdVo = new ShopId($shopId);
        $defaultCategoryId = $this->categoryRepository->getShopDefaultCategory($shopIdVo);

        $product = new Product(null, false, null, $shopId);
        $product->id = $forcedProductId;
        $product->force_id = true;
        $product->active = false;
        $product->id_category_default = $defaultCategoryId->getValue();
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType;
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType;
        $product->product_type = $productType;
        $product->id_shop_default = $shopId;
        $product->name = $localizedNames;
        $product->link_rewrite = array_map(fn (string $name): string => (string) $this->tools->linkRewrite($name), $localizedNames);
        $product->id_tax_rules_group = $this->taxRulesGroupRepository->getIdTaxRulesGroupMostUsed();

        $this->productValidator->validateCreation($product);
        $this->addObjectModelToShops($product, [$shopIdVo], CannotAddProductException::class);
        $this->categoryRepository->addProductAssociations(
            new ProductId((int) $product->id),
            [$defaultCategoryId]
        );
    }

    public function setDateAdd(int $productId, DateTimeImmutable $dateAdd): void
    {
        $formattedDate = $dateAdd->format('Y-m-d H:i:s');

        try {
            $this->connection->executeStatement(
                'UPDATE ' . $this->dbPrefix . 'product SET date_add = :dateAdd WHERE id_product = :productId',
                ['dateAdd' => $formattedDate, 'productId' => $productId]
            );
            $this->connection->executeStatement(
                'UPDATE ' . $this->dbPrefix . 'product_shop SET date_add = :dateAdd WHERE id_product = :productId',
                ['dateAdd' => $formattedDate, 'productId' => $productId]
            );
        } catch (DBALException $e) {
            throw new CannotUpdateProductException(sprintf('Could not set date_add on product %d', $productId), 0, $e);
        }
    }
}
