<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Repository;

use Doctrine\DBAL\Connection;
use Exception;

/**
 * Repository for discount type operations
 */
class DiscountTypeRepository
{
    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix
    ) {
    }

    /**
     * Get all discount types grouped by discount type ID with translations
     *
     * @return array<int, array{id_cart_rule_type: int, discount_type: string, is_core: bool, enabled: bool, names: array<int, string>, descriptions: array<int, string>}>
     */
    public function getAllTypes(): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.discount_type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->orderBy('crt.id_cart_rule_type', 'ASC')
            ->addOrderBy('crtl.id_lang', 'ASC')
        ;

        $allTypes = $qb->executeQuery()->fetchAllAssociative();
        $groupedTypes = [];

        foreach ($allTypes as $type) {
            $discountTypeId = (int) $type['id_cart_rule_type'];
            $langId = !empty($type['id_lang']) ? (int) $type['id_lang'] : null;

            if (!isset($groupedTypes[$discountTypeId])) {
                $groupedTypes[$discountTypeId] = [
                    'id_cart_rule_type' => $discountTypeId,
                    'discount_type' => $type['discount_type'],
                    'is_core' => (bool) $type['is_core'],
                    'enabled' => (bool) $type['active'],
                    'names' => [],
                    'descriptions' => [],
                ];
            }

            // Add translations if available
            if ($langId !== null && !empty($type['name'])) {
                $groupedTypes[$discountTypeId]['names'][$langId] = $type['name'];
            }
            if ($langId !== null && !empty($type['description'])) {
                $groupedTypes[$discountTypeId]['descriptions'][$langId] = $type['description'];
            }
        }

        return $groupedTypes;
    }

    /**
     * Get all active discount types
     *
     * @return array
     */
    public function getAllActiveTypes(int $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.discount_type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->where('crt.active = 1')
            ->andWhere('crtl.id_lang = :languageId')
            ->setParameter('languageId', $languageId)
            ->orderBy('crtl.name')
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Get compatible types for a discount
     *
     * @param int $discountId
     *
     * @return array
     */
    public function getCompatibleTypesIdsForDiscount(int $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type')
            ->from($this->dbPrefix . 'cart_rule_compatible_types', 'crct')
            ->innerJoin('crct', $this->dbPrefix . 'cart_rule_type', 'crt', 'crct.id_cart_rule_type = crt.id_cart_rule_type')
            ->where('crct.id_cart_rule = :discountId')
            ->andWhere('crt.active = 1')
            ->orderBy('crt.id_cart_rule_type')
            ->setParameter('discountId', $discountId)
        ;

        $result = $qb->executeQuery()->fetchAllAssociative();
        if (empty($result)) {
            return [];
        }

        return array_map(fn (array $discountType) => $discountType['id_cart_rule_type'], $result);
    }

    /**
     * Set compatible types for a discount
     *
     * @param int $discountId
     * @param array $compatibleTypeIds
     *
     * @return bool
     */
    public function setCompatibleTypesForDiscount(int $discountId, array $compatibleTypeIds): bool
    {
        $this->connection->beginTransaction();

        try {
            // Remove all existing compatible types
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->delete($this->dbPrefix . 'cart_rule_compatible_types')
                ->where('id_cart_rule = :discountId')
                ->setParameter('discountId', $discountId)
            ;
            $qb->executeStatement();

            // Add new compatible types
            foreach ($compatibleTypeIds as $typeId) {
                $qb = $this->connection->createQueryBuilder();
                $qb
                    ->insert($this->dbPrefix . 'cart_rule_compatible_types')
                    ->values([
                        'id_cart_rule' => ':discountId',
                        'id_cart_rule_type' => ':typeId',
                    ])
                    ->setParameter('discountId', $discountId)
                    ->setParameter('typeId', $typeId)
                ;
                $qb->executeStatement();
            }

            $this->connection->commit();

            return true;
        } catch (Exception $e) {
            $this->connection->rollBack();

            return false;
        }
    }

    /**
     * Check if two discounts are compatible
     *
     * @param int $firstDiscount
     * @param int $secondDiscount
     *
     * @return bool
     */
    public function areDiscountsCompatible(int $firstDiscount, int $secondDiscount): bool
    {
        $firstDiscountType = $this->getDiscountTypeForDiscount($firstDiscount);
        $secondDiscountType = $this->getDiscountTypeForDiscount($secondDiscount);

        if (empty($firstDiscountType) || empty($secondDiscountType)) {
            return true;
        }

        // Check if first discount is compatible with second discount
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('COUNT(*) as count')
            ->from($this->dbPrefix . 'cart_rule_compatible_types', 'crct1')
            ->where('crct1.id_cart_rule = :discountId1')
            ->andWhere('crct1.id_cart_rule_type = :typeId2')
            ->setParameter('discountId1', $firstDiscount)
            ->setParameter('typeId2', $secondDiscountType[0]['id_cart_rule_type'])
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        return $result['count'] > 0;
    }

    /**
     * Get discount type ID by type string
     *
     * @param string $typeString
     *
     * @return int|null
     */
    public function getTypeIdByString(string $typeString): ?int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->where('crt.discount_type = :typeString')
            ->setParameter('typeString', $typeString)
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ? (int) $result['id_cart_rule_type'] : null;
    }

    /**
     * Get discount type by type string
     *
     * @param string $discountType
     *
     * @return array|null
     */
    public function getByDiscountType(string $discountType, int $languageId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.*, crtl.*')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type AND crtl.id_lang = :languageId')
            ->where('crt.discount_type = :discountType')
            ->setParameter('discountType', $discountType)
            ->setParameter('languageId', $languageId)
        ;

        return $qb->executeQuery()->fetchAssociative() ?: null;
    }

    /**
     * Get discount type for a discount
     *
     * @param int $discountId
     *
     * @return array|null
     */
    public function getDiscountTypeForDiscount(int $discountId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.discount_type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule', 'cr')
            ->innerJoin('cr', $this->dbPrefix . 'cart_rule_type', 'crt', 'cr.id_cart_rule_type = crt.id_cart_rule_type')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->where('cr.id_cart_rule = :discountId')
            ->setParameter('discountId', $discountId)
        ;

        $result = $qb->executeQuery()->fetchAllAssociative();

        return empty($result) ? null : $result;
    }

    /**
     * Get discount information including type, priority field, and creation date
     *
     * @return array|null Array with keys: 'id', 'discount_type', 'priority', 'date_add'
     */
    public function getDiscountInfoForPriority(int $discountId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('cr.id_cart_rule', 'crt.discount_type', 'cr.priority', 'cr.date_add')
            ->from($this->dbPrefix . 'cart_rule', 'cr')
            ->leftJoin('cr', $this->dbPrefix . 'cart_rule_type', 'crt', 'cr.id_cart_rule_type = crt.id_cart_rule_type')
            ->where('cr.id_cart_rule = :discountId')
            ->setParameter('discountId', $discountId)
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (!$result) {
            return null;
        }

        return [
            'id' => (int) $result['id_cart_rule'],
            'discount_type' => $result['discount_type'] ?? '',
            'priority' => (int) $result['priority'],
            'date_add' => $result['date_add'],
        ];
    }
}
