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
        $sourceProduct = $this->productRepository->getForShop($productId, $sourceShopId);

        // @todo: for now only fields from product_shop table ar handed, we still need to handle multilang, stock_available, and probably other things (in another PR)
        // @todo: do not forget to copy customization_field_lang, this part was not handled in the legacy import (fixed in this PR)
        // The fields are fetched separately for more clarity, and it could also allow to configure which parts are copied
        // (e.g copy only prices but not stock)
        $fields = array_merge(
            $this->getAssociationFields($sourceProduct),
            $this->getPricesFields($sourceProduct),
            $this->getStockFields($sourceProduct),
            $this->getSEOFields($sourceProduct),
            $this->getOptionsFields($sourceProduct),
            $this->getCustomizationFields($sourceProduct),
            $this->getDateFields($sourceProduct)
        );

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
        return [
            'id_category_default' => (int) $product->id_category_default,
            'cache_default_attribute' => $product->cache_default_attribute,
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getPricesFields(Product $product): array
    {
        return [
            'price' => $product->price,
            'ecotax' => $product->ecotax,
            'id_tax_rules_group' => (int) $product->id_tax_rules_group,
            'on_sale' => $product->on_sale,
            'wholesale_price' => $product->wholesale_price,
            'unit_price_ratio' => $product->unit_price_ratio,
            'unity' => $product->unity,
            'additional_shipping_cost' => $product->additional_shipping_cost,
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getStockFields(Product $product): array
    {
        return [
            'minimal_quantity' => (int) $product->minimal_quantity,
            'low_stock_threshold' => $product->low_stock_threshold,
            'low_stock_alert' => (int) $product->low_stock_alert,
            'advanced_stock_management' => $product->advanced_stock_management,
            'pack_stock_type' => (int) $product->pack_stock_type,
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getSEOFields(Product $product): array
    {
        return [
            'redirect_type' => $product->redirect_type,
            'id_type_redirected' => (int) $product->id_type_redirected,
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getOptionsFields(Product $product): array
    {
        return [
            'condition' => $product->condition,
            'show_condition' => (int) $product->show_condition,
            'show_price' => (int) $product->show_price,
            'visibility' => $product->visibility,
            'on_sale' => (int) $product->on_sale,
            'online_only' => (int) $product->online_only,
            'active' => (int) $product->active,
            'available_for_order' => (int) $product->available_for_order,
            'indexed' => (int) $product->indexed,
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function getCustomizationFields(Product $product): array
    {
        return [
            'customizable' => (int) $product->customizable,
            'uploadable_files' => (int) $product->uploadable_files,
            'text_fields' => (int) $product->text_fields,
        ];
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
            'date_add' => $product->date_add,
            'date_upd' => $now->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
            'available_date' => DateTimeUtil::getNullableDateTime($product->available_date),
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
