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

namespace PrestaShop\PrestaShop\Adapter\CartRule\Repository;

use CartRule;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\CartRule\Validate\CartRuleValidator;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotAddCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotEditCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Restriction\RestrictionRuleGroup;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use Shop;

class CartRuleRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        protected readonly CartRuleValidator $cartRuleValidator,
        protected readonly Connection $connection,
        protected readonly string $dbPrefix
    ) {
    }

    /**
     * @param CartRule $cartRule
     * @param ShopId[] $associatedShopIds
     *
     * @return CartRule
     */
    public function add(CartRule $cartRule, array $associatedShopIds): CartRule
    {
        // cart rule works well with cart_rule_shop table as all the other entities when we add the table association
        // except it uses "shop_restrictions" property for some reason, so we force it to true to avoid breaking legacy code
        //@todo: need to confirm. Is it ok that we always force the shop_restriction = true?
        //       in legacy code shop_restriction = false means rule can be used in all shops
        //       (and it is same if we add all the shops into cart_rule_shop table)
        //       so can we just make sure new code always puts related shops into cart_rule_shop table (even if its single shop context)
        //       and eventually we can get rid of shop_restriction prop?
        //       also the shopTableAssociaton could be moved to Shop:init(), so we don't need to do it in handlers anymore (not sure how would it affect legacy code)
        Shop::addTableAssociation('cart_rule', ['type' => 'shop']);
        $cartRule->shop_restriction = true;

        $this->cartRuleValidator->validate($cartRule);
        $this->addObjectModelToShops($cartRule, $associatedShopIds, CannotAddCartRuleException::class);

        // revert back the default shop table associations in case cartRule is created using legacy method
        Shop::removeTableAssociation('cart_rule');

        return $cartRule;
    }

    public function assertAllCartRulesExists(array $cartRuleIds): void
    {
        $cartRuleIds = array_map(static function (CartRuleId $cartRuleId): int {
            return $cartRuleId->getValue();
        }, $cartRuleIds);

        $qb = $this->connection->createQueryBuilder();

        $result = $qb
            ->select('COUNT(id_cart_rule) AS cart_rules_count')
            ->from($this->dbPrefix . 'cart_rule')
            ->where($qb->expr()->in('id_cart_rule', ':cartRuleIds'))
            ->setParameter('cartRuleIds', $cartRuleIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAssociative()
        ;

        if (!isset($result['cart_rules_count']) || count($cartRuleIds) !== (int) $result['cart_rules_count']) {
            throw new CartRuleNotFoundException('Failed to assert that all provided cart rules exists');
        }
    }

    public function get(CartRuleId $cartRuleId): CartRule
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getObjectModel(
            $cartRuleId->getValue(),
            CartRule::class,
            CartRuleNotFoundException::class
        );

        return $cartRule;
    }

    /**
     * @param CartRuleId $cartRuleId
     * @param RestrictionRuleGroup[] $restrictionRuleGroups
     *
     * @return void
     */
    public function setProductRestrictions(CartRuleId $cartRuleId, array $restrictionRuleGroups): void
    {
        // first remove all product restrictions concerning provided cart rule
        $this->removeProductRestrictions($cartRuleId);

        foreach ($restrictionRuleGroups as $restrictionRuleGroup) {
            $this->connection->createQueryBuilder()
                ->insert($this->dbPrefix . 'cart_rule_product_rule_group')
                ->values([
                    'id_cart_rule' => $cartRuleId->getValue(),
                    'quantity' => $restrictionRuleGroup->getRequiredQuantityInCart(),
                ])
                ->execute()
            ;

            $productRuleGroupId = $this->connection->lastInsertId();

            foreach ($restrictionRuleGroup->getRestrictionRules() as $restrictionRule) {
                $this->connection->createQueryBuilder()
                    ->insert($this->dbPrefix . 'cart_rule_product_rule')
                    ->values([
                        'id_product_rule_group' => ':productRuleGroupId',
                        'type' => ':type',
                    ])
                    ->setParameter('productRuleGroupId', $productRuleGroupId)
                    ->setParameter('type', $restrictionRule->getType())
                    ->execute()
                ;

                $productRuleId = $this->connection->lastInsertId();
                $productRuleValues = [];
                $checkedIds = [];
                foreach ($restrictionRule->getEntityIds() as $id) {
                    if (in_array($id, $checkedIds, true)) {
                        // skip in case there are duplicates
                        continue;
                    }
                    $productRuleValues[] = sprintf('(%d, %d)', $productRuleId, $id);
                    $checkedIds[] = $id;
                }

                $this->connection->prepare('
                    INSERT INTO ' . $this->dbPrefix . 'cart_rule_product_rule_value (id_product_rule, id_item)
                    VALUES ' . implode(',', $productRuleValues)
                )->executeStatement();
            }
        }
    }

    /**
     * @param CartRuleId $cartRuleId
     *
     * @return RestrictionRuleGroup[]
     */
    public function getProductRestrictions(CartRuleId $cartRuleId): array
    {
        // retrieve all item ids based on cart rule
        $ruleValues = $this->connection->createQueryBuilder()
            ->select('crprv.id_product_rule, crprv.id_item')
            ->from($this->dbPrefix . 'cart_rule_product_rule_value', 'crprv')
            ->innerJoin(
                'crprv',
                $this->dbPrefix . 'cart_rule_product_rule',
                'crpr',
                'crprv.id_product_rule = crpr.id_product_rule'
            )
            ->innerJoin(
                'crpr',
                $this->dbPrefix . 'cart_rule_product_rule_group',
                'crprg',
                'crpr.id_product_rule_group = crprg.id_product_rule_group'
            )
            ->where('crprg.id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId->getValue())
            ->execute()
            ->fetchAllAssociative()
        ;

        // retrieve all rules based on cart rule
        $rules = $this->connection->createQueryBuilder()
            ->select('crpr.id_product_rule, crpr.id_product_rule_group, crpr.type')
            ->from($this->dbPrefix . 'cart_rule_product_rule', 'crpr')
            ->innerJoin(
                'crpr',
                $this->dbPrefix . 'cart_rule_product_rule_group',
                'crprg',
                'crpr.id_product_rule_group = crprg.id_product_rule_group'
            )
            ->where('crprg.id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId->getValue())
            ->execute()
            ->fetchAllAssociative()
        ;

        // retrieve all rule groups based on cart rule
        $groups = $this->connection->createQueryBuilder()
            ->select('id_product_rule_group, id_cart_rule, quantity')
            ->from($this->dbPrefix . 'cart_rule_product_rule_group')
            ->where('id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId->getValue())
            ->execute()
            ->fetchAllAssociative()
        ;

        // put quantities under related group
        $quantityForGroups = [];
        foreach ($groups as $group) {
            $productRuleGroupId = (int) $group['id_product_rule_group'];
            $quantityForGroups[$productRuleGroupId] = (int) $group['quantity'];
        }

        // put types under related group and product rules
        $typesForRules = [];
        foreach ($rules as $rule) {
            $productRuleId = (int) $rule['id_product_rule'];
            $typesForRules[$rule['id_product_rule_group']][$productRuleId] = $rule['type'];
        }

        // put ids under related product rules
        $ruleItemIdsForRules = [];
        foreach ($ruleValues as $ruleValue) {
            $productRuleId = (int) $ruleValue['id_product_rule'];
            $ruleItemIdsForRules[$productRuleId][] = (int) $ruleValue['id_item'];
        }

        // finally build the complex restriction rule groups by retrieving related values by array keys structured above
        $restrictionRuleGroups = [];
        foreach ($quantityForGroups as $groupId => $quantity) {
            if (!isset($typesForRules[$groupId])) {
                throw new CartRuleException(
                    sprintf('Unexpected state of cart rule product restrictions. Failed to retrieve types for rules of group %d', $groupId)
                );
            }

            $restrictionRules = [];
            foreach ($typesForRules[$groupId] as $productRuleId => $type) {
                if (!isset($ruleItemIdsForRules[$productRuleId])) {
                    throw new CartRuleException(
                        sprintf('Unexpected state of cart rule product restrictions. Failed to retrieve item ids for rule %d', $productRuleId)
                    );
                }
                $restrictionRules[] = new RestrictionRule($type, $ruleItemIdsForRules[$productRuleId]);
            }

            $restrictionRuleGroups[] = new RestrictionRuleGroup($quantity, $restrictionRules);
        }

        return $restrictionRuleGroups;
    }

    /**
     * @param CartRule $cartRule
     * @param array<int|string, string|int[]> $propertiesToUpdate
     * @param int $errorCode
     */
    public function partialUpdate(CartRule $cartRule, array $propertiesToUpdate, int $errorCode = 0): void
    {
        $this->cartRuleValidator->validate($cartRule);
        $this->partiallyUpdateObjectModel(
            $cartRule,
            $propertiesToUpdate,
            CannotEditCartRuleException::class,
            $errorCode
        );
    }

    /**
     * @param CartRuleId $cartRuleId
     *
     * @return int[]
     */
    public function getRestrictedCartRuleIds(CartRuleId $cartRuleId): array
    {
        $cartRuleIdValue = $cartRuleId->getValue();
        $results = $this->connection->createQueryBuilder()
            ->select('crc.id_cart_rule_1, crc.id_cart_rule_2')
            ->from($this->dbPrefix . 'cart_rule_combination', 'crc')
            ->where('crc.id_cart_rule_1 = :cartRuleId OR crc.id_cart_rule_2 = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleIdValue)
            ->execute()
            ->fetchAllAssociative()
        ;

        if (empty($results)) {
            return [];
        }

        $cartRuleIds = [];
        foreach ($results as $result) {
            $cartRule1 = (int) $result['id_cart_rule_1'];
            $cartRule2 = (int) $result['id_cart_rule_2'];
            // if one column is the id of current cart rule then we append the other column value to array
            $cartRuleIds[] = $cartRule1 === $cartRuleIdValue ? $cartRule2 : $cartRule1;
        }

        return $cartRuleIds;
    }

    /**
     * @param CartRuleId $cartRuleId
     * @param CartRuleId[] $restrictedCartRuleIds
     *
     * @return void
     */
    public function restrictCartRules(CartRuleId $cartRuleId, array $restrictedCartRuleIds): void
    {
        $this->removeRestrictedCartRules($cartRuleId);

        if (empty($restrictedCartRuleIds)) {
            return;
        }

        $checkedIds = [];
        $insertValues = [];
        foreach ($restrictedCartRuleIds as $restrictedCartRuleId) {
            // skip duplicate ids if for some reason they exist
            if (in_array($restrictedCartRuleId, $checkedIds, true)) {
                continue;
            }
            $insertValues[] = sprintf('(%d,%d)', $cartRuleId->getValue(), $restrictedCartRuleId->getValue());
            $checkedIds[] = $restrictedCartRuleId;
        }

        $this->connection->executeStatement(
            sprintf(
                'INSERT INTO %s (`id_cart_rule_1`, `id_cart_rule_2`) VALUES %s',
                $this->dbPrefix . 'cart_rule_combination',
                implode(',', $insertValues)
            )
        );
    }

    private function removeRestrictedCartRules(CartRuleId $cartRuleId): void
    {
        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_combination', 'crc')
            ->where('crc.id_cart_rule_1 = :cartRuleId OR crc.id_cart_rule_2 = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId->getValue())
            ->execute()
        ;
    }

    private function removeProductRestrictions(CartRuleId $cartRuleId): void
    {
        //delete records from cart_rule_product_rule_value for this cart rule
        $this->connection->prepare('
            DELETE crprv
            FROM ' . $this->dbPrefix . 'cart_rule_product_rule_value AS crprv
            INNER JOIN ' . $this->dbPrefix . 'cart_rule_product_rule AS crpr ON crprv.id_product_rule = crpr.id_product_rule
            INNER JOIN ' . $this->dbPrefix . 'cart_rule_product_rule_group AS crprg ON crpr.id_product_rule_group = crprg.id_product_rule_group
            WHERE crprg.id_cart_rule = :cartRuleId
        ')->executeStatement([
            ':cartRuleId' => $cartRuleId->getValue(),
        ]);

        // delete records from cart_rule_product_rule for this cart rule
        $this->connection->prepare('
            DELETE crpr
            FROM  ' . $this->dbPrefix . 'cart_rule_product_rule AS crpr
            INNER JOIN  ' . $this->dbPrefix . 'cart_rule_product_rule_group AS crprg ON crpr.id_product_rule_group = crprg.id_product_rule_group
            WHERE crprg.id_cart_rule = :cartRuleId
        ')->executeStatement([
            ':cartRuleId' => $cartRuleId->getValue(),
        ]);

        // and finally delete records from cart_rule_product_rule_group for this cart rule
        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_product_rule_group')
            ->where('id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId->getValue())
            ->execute()
        ;
    }
}
