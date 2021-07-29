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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Product;

class ProductShopUpdater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductRepository $productRepository
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductRepository $productRepository,
        ShopRepository $shopRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productRepository = $productRepository;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $sourceShopId
     * @param ShopId $targetShopId
     */
    public function copyToShop(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId): void
    {
        $this->shopRepository->assertShopExists($sourceShopId);
        $this->shopRepository->assertShopExists($targetShopId);

        /** @var Product $sourceProduct */
        $sourceProduct = $this->productRepository->get($productId, $sourceShopId);

        $fields = [];
        $fields = $fields + $this->getAssociationFields($sourceProduct);
        $fields = $fields + $this->getPricesFields($sourceProduct);
        $fields = $fields + $this->getStockFields($sourceProduct);
        $fields = $fields + $this->getSEOFields($sourceProduct);
        $fields = $fields + $this->getOptionsFields($sourceProduct);
        $fields = $fields + $this->getCustomizationFields($sourceProduct);
        $fields = $fields + $this->getDateFields($sourceProduct);

        if ($this->productRepository->isAssociatedToShop($productId, $targetShopId)) {
            $qb = $this->getUpdateQueryBuilder($fields, $productId, $targetShopId);
        } else {
            $qb = $this->getInsertQueryBuilder($fields, $productId, $targetShopId);
        }

        foreach ($fields as $column => $value) {
            $qb->setParameter($column, $value);
        }
        $qb->execute();
    }

    /**
     * @param array $fields
     * @param ProductId $productId
     * @param ShopId $targetShopId
     *
     * @return QueryBuilder
     */
    private function getInsertQueryBuilder(
        array $fields,
        ProductId $productId,
        ShopId $targetShopId
    ): QueryBuilder {
        $fields['id_product'] = $productId->getValue();
        $fields['id_shop'] = $targetShopId->getValue();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->insert($this->dbPrefix . 'product_shop')
            ->values($this->formatQueryValues($fields))
            ->setParameter('id_product', $productId->getValue())
            ->setParameter('id_shop', $targetShopId->getValue())
        ;

        return $qb;
    }

    /**
     * @param array $fields
     * @param ProductId $productId
     * @param ShopId $targetShopId
     *
     * @return QueryBuilder
     */
    private function getUpdateQueryBuilder(
        array $fields,
        ProductId $productId,
        ShopId $targetShopId
    ): QueryBuilder {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->update($this->dbPrefix . 'product_shop')
            ->where('id_shop = :id_shop')
            ->where('id_product = :id_product')
            ->setParameter('id_shop', $targetShopId->getValue())
            ->setParameter('id_product', $productId->getValue())
        ;

        $queryValues = $this->formatQueryValues($fields);
        foreach ($queryValues as $column => $value) {
            $qb->set($column, $value);
        }

        return $qb;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getAssociationFields(Product $product): array
    {
        $associationFields = [];
        $associationFields['id_category_default'] = (int) $product->id_category_default;
        $associationFields['cache_default_attribute'] = (int) $product->cache_default_attribute;

        return $associationFields;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getPricesFields(Product $product): array
    {
        $priceFields = [];
        $priceFields['price'] = $product->price;
        $priceFields['ecotax'] = $product->ecotax;
        $priceFields['id_tax_rules_group'] = (int) $product->id_tax_rules_group;
        $priceFields['on_sale'] = $product->on_sale;
        $priceFields['wholesale_price'] = $product->wholesale_price;
        $priceFields['unit_price_ratio'] = $product->unit_price_ratio;
        $priceFields['unity'] = $product->unity;
        $priceFields['additional_shipping_cost'] = $product->additional_shipping_cost;

        return $priceFields;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getStockFields(Product $product): array
    {
        $stockFields = [];
        $stockFields['minimal_quantity'] = (int) $product->minimal_quantity;
        $stockFields['low_stock_threshold'] = $product->low_stock_threshold;
        $stockFields['low_stock_alert'] = (int) $product->low_stock_alert;
        $stockFields['advanced_stock_management'] = $product->advanced_stock_management;
        $stockFields['pack_stock_type'] = (int) $product->pack_stock_type;

        return $stockFields;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getSEOFields(Product $product): array
    {
        $seoFields = [];
        $seoFields['redirect_type'] = $product->redirect_type;
        $seoFields['id_type_redirected'] = (int) $product->id_type_redirected;

        return $seoFields;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getOptionsFields(Product $product): array
    {
        $optionsFields = [];
        $optionsFields['condition'] = $product->condition;
        $optionsFields['show_condition'] = (int) $product->show_condition;
        $optionsFields['show_price'] = (int) $product->show_price;
        $optionsFields['visibility'] = $product->visibility;
        $optionsFields['on_sale'] = (int) $product->on_sale;
        $optionsFields['online_only'] = (int) $product->online_only;
        $optionsFields['active'] = (int) $product->active;
        $optionsFields['available_for_order'] = (int) $product->available_for_order;
        $optionsFields['indexed'] = (int) $product->indexed;

        return $optionsFields;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getCustomizationFields(Product $product): array
    {
        $customizationFields = [];
        $customizationFields['customizable'] = (int) $product->customizable;
        $customizationFields['uploadable_files'] = (int) $product->uploadable_files;
        $customizationFields['text_fields'] = (int) $product->text_fields;

        return $customizationFields;
    }

    /**
     * Date fields are all mandatory.
     *
     * @param Product $product
     *
     * @return array
     */
    private function getDateFields(Product $product): array
    {
        $now = new DateTime();

        return [
            'available_date' => $product->available_date,
            'date_add' => $product->date_add,
            'date_upd' => $now->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
        ];
    }

    /**
     * @param array $fields
     *
     * @return array
     */
    private function formatQueryValues(array $fields): array
    {
        $values = [];
        foreach (array_keys($fields) as $column) {
            $values[sprintf('`%s`', $column)] = sprintf(':%s', $column);
        }

        return $values;
    }
}
