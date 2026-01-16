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
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('crt.id_cart_rule_type', 'crt.discount_type', 'crt.is_core', 'crt.active', 'crtl.name', 'crtl.description', 'crtl.id_lang')
            ->from($this->dbPrefix . 'cart_rule_type', 'crt')
            ->leftJoin('crt', $this->dbPrefix . 'cart_rule_type_lang', 'crtl', 'crt.id_cart_rule_type = crtl.id_cart_rule_type')
            ->where('crt.active = 1')
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
