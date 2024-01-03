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

namespace PrestaShop\PrestaShop\Adapter\Product\Pack\Repository;

use Doctrine\DBAL\Connection;
use Pack;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\QuantifiedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShopException;
use Throwable;

class ProductPackRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param PackId $productId
     * @param LanguageId $languageId
     * @param ShopConstraint $shopConstraint
     *
     * @return array<array<string, string>>
     *                                      e.g [
     *                                      ['id_product_item' => '1', 'id_product_attribute_item' => '1', 'name' => 'Product name', 'reference' => 'demo15', 'quantity' => '1'],
     *                                      ['id_product_item' => '2', 'id_product_attribute_item' => '1', 'name' => 'Product name2', 'reference' => 'demo16', 'quantity' => '1'],
     *                                      ]
     *
     * @throws CoreException
     */
    public function getPackedProducts(PackId $productId, LanguageId $languageId, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId() || $shopConstraint->forAllShops()) {
            throw new InvalidShopConstraintException('Product Pack has no features related with shop group or all shops, use single shop constraint');
        }

        $this->assertProductExists($productId);
        $productIdValue = $productId->getValue();

        try {
            $qb = $this->connection->createQueryBuilder();
            $qb->select('pack.id_product_item, pack.id_product_attribute_item, pack.quantity, attribute.reference as combination_reference, product.reference as product_reference, language.name')
                ->from($this->dbPrefix . 'pack', 'pack')
                ->leftJoin('pack', $this->dbPrefix . 'product', 'product', 'pack.id_product_item = product.id_product')
                ->leftJoin('pack', $this->dbPrefix . 'product_attribute', 'attribute', 'pack.id_product_attribute_item = attribute.id_product_attribute')
                ->leftJoin(
                    'pack',
                    $this->dbPrefix . 'product_lang',
                    'language',
                    // We use product default shop as fallback in case the required shop is not associated to the product
                    'product.id_product = language.id_product AND language.id_lang = :idLanguage AND (language.id_shop = :idShop OR language.id_shop = product.id_shop_default)'
                )
                ->where('pack.id_product_pack = :idProduct')
                ->orderBy('pack.id_product_item', 'ASC')
                ->setParameter('idProduct', $productId->getValue())
                ->setParameter('idLanguage', $languageId->getValue())
                ->setParameter('idShop', $shopConstraint->getShopId()->getValue())
                ->addGroupBy('product.id_product')
                ->addGroupBy('attribute.id_product_attribute')
            ;
            $packedProducts = $qb->executeQuery()->fetchAll();
        } catch (Throwable $exception) {
            throw new CoreException(
                sprintf(
                    'Error occurred when fetching packed products for pack #%d',
                    $productIdValue
                ),
                $exception->getCode(),
                $exception
            );
        }

        return $packedProducts;
    }

    /**
     * @param PackId $packId
     * @param QuantifiedProduct $productForPacking
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function addProductToPack(PackId $packId, QuantifiedProduct $productForPacking): void
    {
        $packIdValue = $packId->getValue();

        try {
            $packed = Pack::addItem(
                $packIdValue,
                $productForPacking->getProductId()->getValue(),
                $productForPacking->getQuantity(),
                $productForPacking->getCombinationId() ?
                    $productForPacking->getCombinationId()->getValue() :
                    NoCombinationId::NO_COMBINATION_ID
            );
            if (!$packed) {
                throw new ProductPackException(
                    $this->appendIdsToMessage('Failed to add product to pack.', $productForPacking, $packIdValue),
                    ProductPackException::FAILED_ADDING_TO_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                $this->appendIdsToMessage('Error occurred when trying to add product to pack.', $productForPacking, $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * @param PackId $packId
     *
     * @throws CoreException
     * @throws ProductPackException
     */
    public function removeAllProductsFromPack(PackId $packId): void
    {
        $packIdValue = $packId->getValue();

        try {
            // We don't reset cache_is_pack for product we want to keep it tru as long as product type doesn't change
            if (!Pack::deleteItems($packIdValue, false)) {
                throw new ProductPackException(
                    sprintf('Failed to remove products from pack #%d', $packIdValue),
                    ProductPackException::FAILED_DELETING_PRODUCTS_FROM_PACK
                );
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to remove pack items from pack #%d', $packIdValue),
                0,
                $e
            );
        }
    }

    /**
     * @param ProductId $productId
     *
     * @return array
     */
    public function getPacksContaining(ProductId $productId): array
    {
        $this->assertProductExists($productId);
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pack.id_product_pack')
            ->from($this->dbPrefix . 'pack', 'pack')
            ->where('pack.id_product_item = :productId')
            ->setParameter('productId', $productId->getValue())
        ;

        $packs = $qb->executeQuery()->fetchAllAssociative();

        return array_map(function (array $packData) {
            return new PackId((int) $packData['id_product_pack']);
        }, $packs);
    }

    /**
     * Builds string with ids, that will help to identify objects that was being updated in case of error
     *
     * @param string $messageBody
     * @param QuantifiedProduct $product
     * @param int $packId
     *
     * @return string
     */
    private function appendIdsToMessage(string $messageBody, QuantifiedProduct $product, int $packId): string
    {
        if ($product->getCombinationId()) {
            $combinationId = sprintf(' combinationId #%d', $product->getCombinationId()->getValue());
        }

        return sprintf(
            "$messageBody. [packId #%d; productId #%d;%s]",
            $packId,
            $product->getProductId()->getValue(),
            isset($combinationId) ? $combinationId : ''
        );
    }

    /**
     * @param ProductId $productId
     */
    protected function assertProductExists(ProductId $productId): void
    {
        $this->assertObjectModelExists($productId->getValue(), 'product', ProductNotFoundException::class);
    }
}
