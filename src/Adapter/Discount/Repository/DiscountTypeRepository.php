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

namespace PrestaShop\PrestaShop\Adapter\Discount\Repository;

use Doctrine\DBAL\Connection;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;

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
     * Get all active discount types
     *
     * @return array
     */
    public function getAllActiveTypes(): array
    {
        $this->addDefaultTypes();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->where('crt.active = 1')
            ->orderBy('crtl.name')
        ;

        return $qb->executeQuery()->fetchAllAssociative();
    }

    /**
     * Add default discount types if they don't exist
     */
    public function addDefaultTypes(): void
    {
        $defaultTypes = [
            ['type' => DiscountType::FREE_SHIPPING, 'name' => 'On free shipping', 'description' => 'Discount that provides free shipping to the order'],
            ['type' => DiscountType::CART_LEVEL, 'name' => 'On cart amount', 'description' => 'Discount applied to cart'],
            ['type' => DiscountType::ORDER_LEVEL, 'name' => 'On total order', 'description' => 'Discount applied to the order'],
            ['type' => DiscountType::CATALOG_LEVEL, 'name' => 'On catalog products', 'description' => 'Discount applied per product unit (multiplies by quantity)'],
            ['type' => DiscountType::FREE_GIFT, 'name' => 'On free gift', 'description' => 'Discount that provides a free gift product'],
        ];

        foreach ($defaultTypes as $typeData) {
            // Check if this specific type already exists to avoid duplicates
            $qb = $this->connection->createQueryBuilder();
            $qb
                ->select('COUNT(*) as count')
                ->from($this->dbPrefix . 'cart_rule_type')
                ->where('type = :type')
                ->setParameter('type', $typeData['type'])
            ;
            $exists = $qb->executeQuery()->fetchAssociative();

            if ($exists['count'] > 0) {
                continue;
            }

            $qb = $this->connection->createQueryBuilder();
            $qb
                ->insert($this->dbPrefix . 'cart_rule_type')
                ->values([
                    'type' => ':type',
                    'is_core' => 1,
                    'active' => 1,
                    'date_add' => 'NOW()',
                    'date_upd' => 'NOW()',
                ])
                ->setParameter('type', $typeData['type'])
            ;
            $qb->executeStatement();
            $typeId = (int) $this->connection->lastInsertId();

            $qb = $this->connection->createQueryBuilder();
            $qb
                ->insert($this->dbPrefix . 'cart_rule_type_lang')
                ->values([
                    'id_cart_rule_type' => ':typeId',
                    'id_lang' => 1,
                    'name' => ':name',
                    'description' => ':description',
                ])
                ->setParameter('typeId', $typeId)
                ->setParameter('name', $typeData['name'])
                ->setParameter('description', $typeData['description'])
            ;
            $qb->executeStatement();
        }
    }

    /**
     * Get compatible types for a discount
     *
     * @param int $discountId
     *
     * @return array
     */
    public function getCompatibleTypesForDiscount(int $discountId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule_compatible_types', 'crct')
            ->innerJoin('crct', $this->dbPrefix . 'cart_rule_type', 'crt', 'crct.id_cart_rule_type = crt.id_cart_rule_type')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->where('crct.id_cart_rule = :discountId')
            ->andWhere('crt.active = 1')
            ->orderBy('crtl.name')
            ->setParameter('discountId', $discountId)
        ;

        return $qb->executeQuery()->fetchAllAssociative();
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
        // Ensure default types exist
        $this->addDefaultTypes();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->where('crt.type = :typeString')
            ->setParameter('typeString', $typeString)
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ? (int) $result['id_cart_rule_type'] : null;
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
            ->select('crt.id_cart_rule_type', 'crt.type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
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
     * @return array|null Array with keys: 'id', 'type', 'priority', 'date_add'
     */
    public function getDiscountInfoForPriority(int $discountId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('cr.id_cart_rule', 'crt.type', 'cr.priority', 'cr.date_add')
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
            'type' => $result['type'] ?? '',
            'priority' => (int) $result['priority'],
            'date_add' => $result['date_add'],
        ];
    }
}
