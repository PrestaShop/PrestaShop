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

namespace PrestaShop\PrestaShop\Adapter;

use Combination;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Product\ProductDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Product;

/**
 * This class will provide data from DB / ORM about product combination.
 */
class CombinationDataProvider
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var ProductDataProvider
     */
    private $productAdapter;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Locale $locale
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Locale $locale,
        Connection $connection,
        string $dbPrefix
    ) {
        $this->context = new LegacyContext();
        $this->productAdapter = new ProductDataProvider();
        $this->locale = $locale;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Get a combination values.
     *
     * @deprecated since 1.7.3.1 really slow, use getFormCombinations instead.
     *
     * @param int $combinationId The id_product_attribute
     *
     * @return array combinations
     */
    public function getFormCombination($combinationId)
    {
        $product = new Product((new Combination($combinationId))->id_product);

        return $this->completeCombination(
            $product->getAttributeCombinationsById(
                $combinationId,
                $this->context->getContext()->language->id
            ),
            $product
        );
    }

    /**
     * Retrieve combinations data for a specific language id.
     *
     * @param array $combinationIds
     * @param int $languageId
     *
     * @return array a list of formatted combinations
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getFormCombinations(array $combinationIds, $languageId)
    {
        $productId = (new Combination($combinationIds[0]))->id_product;
        $product = new Product($productId);
        $combinations = [];

        foreach ($combinationIds as $combinationId) {
            $combinations[$combinationId] = $this->completeCombination(
                $product->getAttributeCombinationsById(
                    $combinationId,
                    $languageId
                ),
                $product
            );
        }

        return $combinations;
    }

    /**
     * @param array $attributesCombinations
     * @param Product $product
     *
     * @return array
     */
    public function completeCombination($attributesCombinations, $product)
    {
        $combination = $attributesCombinations[0];

        $attribute_price_impact = 0;
        if ($combination['price'] > 0) {
            $attribute_price_impact = 1;
        } elseif ($combination['price'] < 0) {
            $attribute_price_impact = -1;
        }

        $attribute_weight_impact = 0;
        if ($combination['weight'] > 0) {
            $attribute_weight_impact = 1;
        } elseif ($combination['weight'] < 0) {
            $attribute_weight_impact = -1;
        }

        $attribute_unity_price_impact = 0;
        if ($combination['unit_price_impact'] > 0) {
            $attribute_unity_price_impact = 1;
        } elseif ($combination['unit_price_impact'] < 0) {
            $attribute_unity_price_impact = -1;
        }

        $finalPrice = (new DecimalNumber((string) $product->price))
            ->plus(new DecimalNumber((string) $combination['price']))
            ->toPrecision(CommonAbstractType::PRESTASHOP_DECIMALS);

        return [
            'id_product_attribute' => $combination['id_product_attribute'],
            'attribute_reference' => $combination['reference'],
            'attribute_ean13' => $combination['ean13'],
            'attribute_isbn' => $combination['isbn'],
            'attribute_upc' => $combination['upc'],
            'attribute_mpn' => $combination['mpn'],
            'attribute_wholesale_price' => $combination['wholesale_price'],
            'attribute_price_impact' => $attribute_price_impact,
            'attribute_price' => $combination['price'],
            'attribute_price_display' => $this->locale->formatPrice($combination['price'], $this->context->getContext()->currency->iso_code),
            'final_price' => (string) $finalPrice,
            'attribute_priceTI' => '',
            'attribute_ecotax' => $combination['ecotax'],
            'attribute_weight_impact' => $attribute_weight_impact,
            'attribute_weight' => $combination['weight'],
            'attribute_unit_impact' => $attribute_unity_price_impact,
            'attribute_unity' => $combination['unit_price_impact'],
            'attribute_minimal_quantity' => $combination['minimal_quantity'],
            'attribute_low_stock_threshold' => $combination['low_stock_threshold'],
            'attribute_low_stock_alert' => (bool) $combination['low_stock_alert'],
            'available_date_attribute' => $combination['available_date'],
            'attribute_default' => (bool) $combination['default_on'],
            'attribute_location' => $this->productAdapter->getLocation($product->id, $combination['id_product_attribute']),
            'attribute_quantity' => $this->productAdapter->getQuantity($product->id, $combination['id_product_attribute']),
            'name' => $this->getCombinationName($attributesCombinations),
            'id_product' => $product->id,
        ];
    }

    /**
     * @param int $productId
     * @param int $limit
     * @param int $offset
     * @param array $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getProductCombinations(int $productId, ?int $limit = null, ?int $offset = null, array $filters = []): array
    {
        $qb = $this->getCombinationsQueryBuilder($productId, $filters)
            ->select('pa.*')
            ->setParameter('productId', $productId)
        ;

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return  $qb->execute()->fetchAll();
    }

    /**
     * @param int $productId
     * @param array $filters
     *
     * @return int
     */
    public function getTotalCombinationsCount(int $productId, array $filters = []): int
    {
        $qb = $this->getCombinationsQueryBuilder($productId, $filters)->select('COUNT(pa.id_product_attribute) AS total_combinations');

        return (int) $qb->execute()->fetch()['total_combinations'];
    }

    /**
     * @param int[] $combinationIds
     * @param LanguageId $langId
     *
     * @return array<int, array<int, mixed>>
     */
    public function getAttributesInfoByCombinationIds(array $combinationIds, LanguageId $langId): array
    {
        $attributeCombinationAssociations = $this->getAttributeCombinationAssociations($combinationIds);

        $attributeIds = array_unique(array_map(function ($attributeByCombination) {
            return $attributeByCombination['id_attribute'];
        }, $attributeCombinationAssociations));

        $attributesInfoByAttributeId = $this->getAttributesInformation($attributeIds, $langId->getValue());

        $attributesInfoByCombinationId = [];
        foreach ($attributeCombinationAssociations as $attributeCombinationAssociation) {
            $combinationId = (int) $attributeCombinationAssociation['id_product_attribute'];
            $attributeId = (int) $attributeCombinationAssociation['id_attribute'];
            $attributesInfoByCombinationId[$combinationId] = $attributesInfoByAttributeId[$attributeId];
        }

        return $attributesInfoByCombinationId;
    }

    /**
     * @param array $attributesCombinations
     *
     * @return string
     */
    private function getCombinationName($attributesCombinations)
    {
        $name = [];

        foreach ($attributesCombinations as $attribute) {
            $name[] = $attribute['group_name'] . ' - ' . $attribute['attribute_name'];
        }

        return implode(', ', $name);
    }

    /**
     * @param int[] $combinationIds
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAttributeCombinationAssociations(array $combinationIds): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_attribute')
            ->addSelect('pac.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->where($qb->expr()->in('pac.id_product_attribute', ':combinationIds'))
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
        ;

        return $qb->execute()->fetchAll();
    }

    /**
     * @param int[] $attributeIds
     * @param int $langId
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAttributesInformation(array $attributeIds, int $langId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.id_attribute')
            ->addSelect('ag.id_attribute_group')
            ->addSelect('al.name AS attribute_name')
            ->addSelect('agl.name AS attribute_group_name')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_lang',
                'al',
                'a.id_attribute = al.id_attribute AND al.id_lang = :langId'
            )->leftJoin(
                'a',
                $this->dbPrefix . 'attribute_group',
                'ag',
                'a.id_attribute_group = ag.id_attribute_group'
            )->leftJoin(
                'ag',
                $this->dbPrefix . 'attribute_group_lang',
                'agl',
                'agl.id_attribute_group = ag.id_attribute_group AND agl.id_lang = :langId'
            )->where($qb->expr()->in('a.id_attribute', ':attributeIds'))
            ->setParameter('attributeIds', $attributeIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('langId', $langId)
        ;

        $attributesInfo = $qb->execute()->fetchAll();

        $attributesInfoByAttributeId = [];
        foreach ($attributesInfo as $attributeInfo) {
            $attributesInfoByAttributeId[(int) $attributeInfo['id_attribute']][] = $attributeInfo;
        }

        return $attributesInfoByAttributeId;
    }

    /**
     * @param int $productId
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getCombinationsQueryBuilder(int $productId, array $filters): QueryBuilder
    {
        //@todo: filters are not handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
        ;

        return $qb;
    }
}
