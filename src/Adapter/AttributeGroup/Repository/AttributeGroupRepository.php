<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository;

use AttributeGroup;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\CannotAddAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\CannotUpdateAttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

class AttributeGroupRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        private Connection $connection,
        private string $dbPrefix,
    ) {
    }

    /**
     * @param AttributeGroup $attributeGroup
     *
     * @return AttributeGroupId
     *
     * @throws CoreException
     */
    public function add(AttributeGroup $attributeGroup): AttributeGroupId
    {
        $attributeGroupId = $this->addObjectModelToShops(
            $attributeGroup,
            array_map(fn (int $shopId) => new ShopId((int) $shopId), $attributeGroup->id_shop_list),
            CannotAddAttributeGroupException::class
        );

        return new AttributeGroupId($attributeGroupId);
    }

    public function partialUpdate(AttributeGroup $attribute, array $propertiesToUpdate, int $errorCode = 0): void
    {
        $this->partiallyUpdateObjectModel($attribute, $propertiesToUpdate, CannotUpdateAttributeGroupException::class, $errorCode);
        $this->updateObjectModelShopAssociations(
            (int) $attribute->id,
            AttributeGroup::class,
            $attribute->id_shop_list
        );
    }

    /**
     * @param AttributeGroupId $attributeGroupId
     *
     * @return AttributeGroup
     *
     * @throws ShopAssociationNotFound
     * @throws CoreException
     */
    public function get(AttributeGroupId $attributeGroupId): AttributeGroup
    {
        /** @var AttributeGroup $attributeGroup */
        $attributeGroup = $this->getObjectModel(
            $attributeGroupId->getValue(),
            AttributeGroup::class,
            AttributeGroupNotFoundException::class
        );

        return $attributeGroup;
    }

    /**
     * @param ShopConstraint $shopConstraint
     * @param AttributeGroupId[] $attributeGroupIds get only certain attribute groups (e.g. when need to get only certain combinations attributes groups)
     *
     * @return array<int, AttributeGroup> array key is the id of attribute group
     */
    public function getAttributeGroups(ShopConstraint $shopConstraint, array $attributeGroupIds = []): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Shop Group constraint is not supported');
        }
        $shopIdValue = $shopConstraint->getShopId() ? $shopConstraint->getShopId()->getValue() : null;
        $qb = $this->connection->createQueryBuilder()
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

        if (!empty($attributeGroupIds)) {
            $attributeGroupIdValues = array_map(static function (AttributeGroupId $attributeGroupId): int {
                return $attributeGroupId->getValue();
            }, $attributeGroupIds);

            $qb->andWhere($qb->expr()->in('ag.id_attribute_group', ':attributeGroupIds'))
                ->setParameter('attributeGroupIds', $attributeGroupIdValues, Connection::PARAM_INT_ARRAY)
            ;
        }

        if ($shopIdValue) {
            $qb
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

        $results = $qb->executeQuery()->fetchAllAssociative();

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
     * Asserts that attribute groups exists in all the provided shops.
     * If at least one of them is missing in any shop, it throws exception.
     *
     * @param AttributeGroupId[] $attributeGroupIds
     * @param ShopId[] $shopIds
     *
     * @throws ShopAssociationNotFound
     */
    public function assertExistsInEveryShop(array $attributeGroupIds, array $shopIds): void
    {
        $attributeGroupIdValues = array_map(static function (AttributeGroupId $attributeGroupId): int {
            return $attributeGroupId->getValue();
        }, $attributeGroupIds);

        $shopIdValues = array_map(static function (ShopId $shopId): int {
            return $shopId->getValue();
        }, $shopIds);

        $qb = $this->connection->createQueryBuilder();
        $results = $qb
            ->select('ags.id_shop, ag.id_attribute_group')
            ->from($this->dbPrefix . 'attribute_group', 'ag')
            ->innerJoin(
                'ag',
                $this->dbPrefix . 'attribute_group_shop',
                'ags',
                'ag.id_attribute_group = ags.id_attribute_group AND ags.id_shop IN (:shopIds)'
            )
            ->andWhere($qb->expr()->in('ag.id_attribute_group', ':attributeGroupIds'))
            ->setParameter('shopIds', $shopIdValues, Connection::PARAM_INT_ARRAY)
            ->setParameter('attributeGroupIds', $attributeGroupIdValues, Connection::PARAM_INT_ARRAY)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $attributeGroupShops = [];
        foreach ($results as $result) {
            $attributeGroupShops[(int) $result['id_attribute_group']][] = (int) $result['id_shop'];
        }

        foreach ($attributeGroupIdValues as $attributeGroupIdValue) {
            if (!isset($attributeGroupShops[$attributeGroupIdValue]) || $attributeGroupShops[$attributeGroupIdValue] !== $shopIdValues) {
                throw new ShopAssociationNotFound('Provided attribute groups does not exist in every shop');
            }
        }
    }
}
