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
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetProductCombinationsForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler\GetProductCombinationsForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationForEditing;

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
    public function handle(GetProductCombinationsForEditing $query): array
    {
        $product = $this->getProduct($query->getProductId());
        $combinations = $this->getCombinations((int) $product->id, $query->getOffset(), $query->getLimit());

        $combinationIds = array_map(function ($combination) {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->getAttributesInformationByCombinationId($combinationIds, $query->getLanguageId());

        return $this->formatCombinationsForEditing($combinations, $attributesInformation);
    }

    /**
     * @param array $combinations
     * @param array<int, ProductCombinationForEditing> $attributesInformationByCombinationId
     *
     * @return ProductCombinationForEditing[]
     */
    private function formatCombinationsForEditing(array $combinations, array $attributesInformationByCombinationId): array
    {
        $combinationsForEditing = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $combinationAttributesInformation = $attributesInformationByCombinationId[$combinationId];

            $combinationsForEditing[] = new ProductCombinationForEditing(
                $combinationId,
                $this->buildCombinationName($combinationAttributesInformation),
                $combinationAttributesInformation
            );
        }

        return $combinationsForEditing;
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
        $qb->select('a.id_attribute')
            ->addSelect('pac.id_product_attribute')
            ->addSelect('ag.id_attribute_group')
            ->addSelect('al.name AS attribute_name')
            ->addSelect('agl.name AS attribute_group_name')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->leftJoin(
                'pac',
                $this->dbPrefix . 'attribute',
                'a',
                'pac.id_attribute = a.id_attribute'
            )->leftJoin(
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
            )->where($qb->expr()->in('pac.id_product_attribute', ':combinationIds'))
            ->setParameter('combinationIds', $combinationIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('langId', $langId->getValue())
        ;

        $results = $qb->execute()->fetchAll();

        $attributesInformationByCombinationId = [];
        foreach ($results as $result) {
            $attributesInformationByCombinationId[(int) $result['id_product_attribute']][] = new CombinationAttributeInformation(
                (int) $result['id_attribute_group'],
                $result['attribute_group_name'],
                (int) $result['id_attribute'],
                $result['attribute_name']
            );
        }

        return $attributesInformationByCombinationId;
    }

    /**
     * @param int $productId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    private function getCombinations(int $productId, int $offset, int $limit): array
    {
        $qb = $this->connection->createQueryBuilder();
        //@todo: select all necessary values for combination editing like quantity, price etc. (UpdateCombinations PR)
        //@todo: maybe add an object like `CombinationInformation` for this?
        $qb->select('pa.id_product_attribute')
            ->addSelect('pa.id_product')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->execute()->fetchAll();
    }
}
