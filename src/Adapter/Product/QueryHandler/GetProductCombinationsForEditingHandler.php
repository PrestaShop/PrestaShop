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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetProductCombinationsForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetProductCombinationsForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsForEditing;

/**
 * Handles @see GetProductCombinationsForEditing using legacy object model
 */
final class GetProductCombinationsForEditingHandler extends AbstractProductHandler implements GetProductCombinationsForEditingHandlerInterface
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
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetProductCombinationsForEditing $query): ProductCombinationsForEditing
    {
        $product = $this->getProduct($query->getProductId());
        $combinationsQb = $this->getCombinationsQueryBuilder((int) $product->id, $query->getOffset(), $query->getLimit());
        $combinations = $this->getCombinations($combinationsQb);

        $combinationIds = array_map(function ($combination): int {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->getAttributesInformationByCombinationId($combinationIds, $query->getLanguageId());

        return $this->formatCombinationsForEditing(
            $combinations,
            $attributesInformation,
            $this->getTotalCombinationsCount($combinationsQb)
        );
    }

    /**
     * @param QueryBuilder $combinationsQb
     *
     * @return array
     */
    private function getCombinations(QueryBuilder $combinationsQb): array
    {
        //@todo: select all necessary values for combination editing like quantity, price etc. (UpdateCombinations PR)
        //@todo: check Product::getAttributeCombinations for quantities
        //@todo: Add an object like `CombinationInformation` for this?
        return $combinationsQb
            ->select('pa.id_product_attribute')
            ->execute()
            ->fetchAll()
        ;
    }

    /**
     * @param array $combinations
     * @param array<int, CombinationForEditing> $attributesInformationByCombinationId
     * @param int $totalCombinationsCount
     *
     * @return ProductCombinationsForEditing
     */
    private function formatCombinationsForEditing(
        array $combinations,
        array $attributesInformationByCombinationId,
        int $totalCombinationsCount
    ): ProductCombinationsForEditing {
        $combinationsForEditing = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $combinationAttributesInformation = $attributesInformationByCombinationId[$combinationId];

            $combinationsForEditing[] = new CombinationForEditing(
                $combinationId,
                $this->buildCombinationName($combinationAttributesInformation),
                $combinationAttributesInformation
            );
        }

        return new ProductCombinationsForEditing($totalCombinationsCount, $combinationsForEditing);
    }

    /**
     * @param CombinationAttributeInformation[] $attributesInformation
     *
     * @return string
     */
    private function buildCombinationName(array $attributesInformation): string
    {
        $combinedNameParts = [];
        foreach ($attributesInformation as $combinationAttributeInformation) {
            $combinedNameParts[] = sprintf(
                '%s - %s',
                $combinationAttributeInformation->getAttributeGroupName(),
                $combinationAttributeInformation->getAttributeName()
            );
        }

        return implode(', ', $combinedNameParts);
    }

    /**
     * @param int[] $combinationIds
     * @param LanguageId $langId
     *
     * @todo: move queries to some dedicated service. Or whole method to Combination|Product obj model?
     *
     * @return CombinationAttributeInformation[]
     */
    private function getAttributesInformationByCombinationId(array $combinationIds, LanguageId $langId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_attribute')
            ->addSelect('pac.id_product_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->where($qb->expr()->in('pac.id_product_attribute', ':combinationIds'))
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
        ;

        $attributeCombinationAssociations = $qb->execute()->fetchAll();

        $attributeIds = array_unique(array_map(function ($attributeByCombination) {
            return $attributeByCombination['id_attribute'];
        }, $attributeCombinationAssociations));

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
            ->setParameter('langId', $langId->getValue())
        ;

        $attributesInfo = $qb->execute()->fetchAll();

        $attributesInfoByAttributeId = [];
        foreach ($attributesInfo as $attributeInfo) {
            $attributesInfoByAttributeId[(int) $attributeInfo['id_attribute']][] = new CombinationAttributeInformation(
                (int) $attributeInfo['id_attribute_group'],
                $attributeInfo['attribute_group_name'],
                (int) $attributeInfo['id_attribute'],
                $attributeInfo['attribute_name']
            );
        }

        $attributesInformationByCombinationId = [];
        foreach ($attributeCombinationAssociations as $attributeCombinationAssociation) {
            $combinationId = (int) $attributeCombinationAssociation['id_product_attribute'];
            $attributeId = (int) $attributeCombinationAssociation['id_attribute'];
            $attributesInformationByCombinationId[$combinationId] = $attributesInfoByAttributeId[$attributeId];
        }

        return $attributesInformationByCombinationId;
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return int
     */
    private function getTotalCombinationsCount(QueryBuilder $qb): int
    {
        $qb->select('COUNT(pa.id_product_attribute) AS total_combinations');

        return (int) $qb->execute()->fetch()['total_combinations'];
    }

    /**
     * @param int $productId
     * @param int $offset
     * @param int $limit
     *
     * @return QueryBuilder
     */
    private function getCombinationsQueryBuilder(int $productId, int $offset, int $limit): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        return $qb;
    }
}
