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

namespace PrestaShop\PrestaShop\Adapter\Attribute\Repository;

use AttributeGroup;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use ProductAttribute;
use RuntimeException;

/**
 * Provides access to attribute data source
 */
class AttributeRepository extends AbstractObjectModelRepository
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
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        ProductMultiShopRepository $productRepository
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int[] $attributeIds
     */
    public function assertAllAttributesExist(array $attributeIds): void
    {
        if (empty($attributeIds)) {
            throw new RuntimeException('Empty list of attribute ids provided');
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id_attribute) AS total')
            ->from($this->dbPrefix . 'attribute')
            ->where('id_attribute IN (:idsList)')
            ->setParameter('idsList', $attributeIds, Connection::PARAM_INT_ARRAY)
        ;

        $result = (int) $qb->execute()->fetch()['total'];

        if (count($attributeIds) !== $result) {
            throw new AttributeNotFoundException('Some of provided attributes does not exist');
        }
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return array<int, AttributeGroup> array key is the id of attribute group
     */
    public function getAttributeGroups(ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop Group constraint is not supported');

        }
        $shopIdValue = $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;
        $groupsQb =
            $this->connection->createQueryBuilder()
            ->select('ag.*, agl.*')
            ->from($this->dbPrefix . 'attribute_group', 'ag')
            ->innerJoin(
                'ag',
                $this->dbPrefix . 'attribute_group_lang',
                'agl',
                'ag.id_attribute_group = agl.id_attribute_group'
            )
            ->orderBy('ag.position', 'ASC')
        ;

        if ($shopIdValue) {
            $groupsQb
                ->innerJoin(
                    'ag',
                    $this->dbPrefix . 'attribute_group_shop',
                    'ags',
                    'ag.id_attribute_group = ags.id_attribute_group'
                )
                ->andWhere('ags.id_shop = :shopId')
                ->setParameter('shopId', $shopIdValue)
            ;
        }

        $results = $groupsQb->execute()->fetchAllAssociative();

        if (!$results) {
            return [];
        }

        $attributeGroups = [];

        foreach ($results as $result) {
            $attributeGroupId = (int) $result['id_attribute_group'];
            $langId = (int) $result['id_lang'];

            if (isset($attributeGroups[$attributeGroupId])) {
                $attributeGroup = $attributeGroups[$attributeGroupId];
            } else {
                $attributeGroup = new AttributeGroup();
                $attributeGroups[$attributeGroupId] = $attributeGroup;
            }

            $attributeGroup->id = $attributeGroupId;
            $attributeGroup->is_color_group = (bool) $result['is_color_group'];
            $attributeGroup->group_type = (string) $result['group_type'];
            $attributeGroup->position = (int) $result['position'];
            $attributeGroup->name[$langId] = (string) $result['name'];
            $attributeGroup->public_name[$langId] = (string) $result['public_name'];
        }

        return $attributeGroups;
    }

    /**
     * @param ShopConstraint $shopConstraint
     * @param int[] $attributeGroupIds
     *
     * @return array<int, array<int, ProductAttribute>> arrays of product attributes indexed by product attribute groups
     */
    public function getGroupAttributes(ShopConstraint $shopConstraint, array $attributeGroupIds): array
    {
        if (empty($attributeGroupIds)) {
            return [];
        }

        $attributesQb = $this->connection->createQueryBuilder();
        $attributesQb
            ->select('a.*, al.*')
            ->from($this->dbPrefix . 'attribute', 'a')
            ->innerJoin(
                'a',
                $this->dbPrefix . 'attribute_lang',
                'al',
                'a.id_attribute = al.id_attribute'
            )
            ->andWhere($attributesQb->expr()->in('a.id_attribute_group', ':attributeGroupIds'))
            ->setParameter('attributeGroupIds', $attributeGroupIds, Connection::PARAM_INT_ARRAY)
        ;

        $shopIdValue = $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;

        if ($shopIdValue) {
            $attributesQb
                ->leftJoin(
                    'a',
                    $this->dbPrefix . 'attribute_shop',
                    'attr_shop',
                    'a.id_attribute = attr_shop.id_attribute'
                )
                ->andWhere('attr_shop.id_shop = :shopId')
                ->setParameter('shopId', $shopIdValue)
            ;
        }

        $results = $attributesQb->execute()->fetchAllAssociative();

        if (!$results) {
            return [];
        }

        $attributes = [];

        foreach ($results as $result) {
            $attributeGroupId = (int) $result['id_attribute_group'];
            $attributeId = (int) $result['id_attribute'];
            $langId = (int) $result['id_lang'];

            if (isset($attributes[$attributeGroupId][$attributeId])) {
                $attribute = $attributes[$attributeId];
            } else {
                $attribute = new ProductAttribute();
                $attributes[$attributeGroupId][$attributeId] = $attribute;
            }

            $attribute->id = $attributeId;
            $attribute->id_attribute_group = $attributeGroupId;
            $attribute->color = (string) $result['color'];
            $attribute->position = (int) $result['position'];
            $attribute->name[$langId] = (string) $result['name'];
        }

        return $attributes;
    }

    /**
     * @param ProductId $productId
     *
     * @return array<int>
     */
    public function getProductAttributesIds(ProductId $productId, ShopConstraint $shopConstraint): array
    {
        $shopIds = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint));

        $qb = $this->connection->createQueryBuilder();
        $qb->select('pac.id_attribute')
            ->from($this->dbPrefix . 'product_attribute_combination', 'pac')
            ->innerJoin(
                'pac',
                $this->dbPrefix . 'product_attribute_shop',
                'pas',
                'pac.id_product_attribute = pas.id_product_attribute'
            )
            ->where('pas.id_product = :productId')
            ->andWhere($qb->expr()->in('pas.id_shop', ':shopIds'))
            ->setParameter('productId', $productId->getValue())
            ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY)
            ->groupBy('pac.id_attribute')
        ;

        return $qb->execute()->fetchAll(FetchMode::COLUMN);
    }

    /**
     * @param CombinationId[] $combinationIds
     * @param LanguageId $langId
     *
     * @return array<int, CombinationAttributeInformation[]>
     */
    public function getAttributesInfoByCombinationIds(array $combinationIds, LanguageId $langId): array
    {
        $attributeCombinationAssociations = $this->getAttributeCombinationAssociations($combinationIds);
        $attributeIds = array_unique(array_map(static function (array $attributeByCombination): int {
            return (int) $attributeByCombination['id_attribute'];
        }, $attributeCombinationAssociations));

        $attributesInfoByAttributeId = $this->getAttributesInformation($attributeIds, $langId->getValue());

        return $this->buildCombinationAttributeInformationList(
            $attributeCombinationAssociations,
            $attributesInfoByAttributeId
        );
    }

    /**
     * @param CombinationId[] $combinationIds
     *
     * @return array<int, array<string, mixed>>
     */
    private function getAttributeCombinationAssociations(array $combinationIds): array
    {
        if (empty($combinationIds)) {
            return [];
        }

        $combinationIds = array_map(function (CombinationId $id): int {
            return $id->getValue();
        }, $combinationIds);

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
        $qb->select('a.id_attribute, a.position, a.color')
            ->addSelect('ag.id_attribute_group')
            ->addSelect('al.name AS attribute_name')
            ->addSelect('agl.name AS attribute_group_name')
            ->addSelect('agl.public_name AS attribute_group_public_name')
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
            ->addOrderBy('ag.position', 'ASC')
            ->addOrderBy('a.position', 'ASC')
            ->setParameter('attributeIds', $attributeIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('langId', $langId)
        ;

        $attributesInfo = $qb->execute()->fetchAll();

        $attributesInfoByAttributeId = [];
        foreach ($attributesInfo as $attributeInfo) {
            $attributesInfoByAttributeId[(int) $attributeInfo['id_attribute']] = $attributeInfo;
        }

        return $attributesInfoByAttributeId;
    }

    /**
     * @param array<int, array<string, int>> $attributeCombinationAssociations
     * @param array<int, array<string, mixed>> $attributesInfoByAttributeId
     *
     * @return array<int, CombinationAttributeInformation[]>
     */
    private function buildCombinationAttributeInformationList(
        array $attributeCombinationAssociations,
        array $attributesInfoByAttributeId
    ): array {
        $attributesInfoByCombinationId = [];
        foreach ($attributeCombinationAssociations as $attributeCombinationAssociation) {
            $combinationId = (int) $attributeCombinationAssociation['id_product_attribute'];
            $attributeId = (int) $attributeCombinationAssociation['id_attribute'];
            $attributesInfoByCombinationId[$combinationId][] = new CombinationAttributeInformation(
                (int) $attributesInfoByAttributeId[$attributeId]['id_attribute_group'],
                $attributesInfoByAttributeId[$attributeId]['attribute_group_name'],
                (int) $attributesInfoByAttributeId[$attributeId]['id_attribute'],
                $attributesInfoByAttributeId[$attributeId]['attribute_name']
            );
        }

        return $attributesInfoByCombinationId;
    }
}
