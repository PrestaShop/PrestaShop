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
        //@todo: hardcoded pagination
        $paginatedCombinations = $this->getPaginatedCombinations((int) $product->id, 0, 5);
        $attributesInformation = $this->getAttributesInformation($paginatedCombinations);

        return $this->formatCombinationsForEditing($attributesInformation);
    }

    /**
     * @param array $combinations
     *
     * @return ProductCombinationForEditing[]
     */
    private function formatCombinationsForEditing(array $combinations): array
    {
        $combinationsForEditing = [];
        $attributesInformationByCombinationId = [];

        foreach ($combinations as $combination) {
            $combinationId = (int) $combination['id_product_attribute'];
            $attributesInformationByCombinationId[$combinationId][] = new CombinationAttributeInformation(
                (int) $combination['id_attribute_group'],
                $combination['attribute_group_name'],
                (int) $combination['id_attribute'],
                $combination['attribute_name']
            );
        }

        foreach ($attributesInformationByCombinationId as $combinationId => $attributesInformation) {
            $combinationsForEditing[] = new ProductCombinationForEditing(
                $combinationId,
                $this->buildCombinationName($attributesInformation),
                $attributesInformation
            );
        }

        return $combinationsForEditing;
    }

    /**
     * @param array $attributesInformation
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
     * @param int[] $paginatedCombinations
     *
     * @return array
     */
    private function getAttributesInformation(array $paginatedCombinations): array
    {
        //@todo:
        $langId = \Configuration::get('PS_LANG_DEFAULT');

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
            ->setParameter('combinationIds', $paginatedCombinations, Connection::PARAM_INT_ARRAY)
            ->setParameter('langId', $langId)
        ;

        return $qb->execute()->fetchAll();
    }

    private function getPaginatedCombinations(int $productId, int $offset, int $limit): array
    {
        //@todo: retrieve more values than id
        $qb = $this->connection->createQueryBuilder();
        $qb->select('pa.id_product_attribute')
            ->addSelect('pa.id_product')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->where('pa.id_product = :productId')
            ->setParameter('productId', $productId)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return array_map(function ($result) {
            return (int) $result['id_product_attribute'];
        }, $qb->execute()->fetchAll());
    }
}
