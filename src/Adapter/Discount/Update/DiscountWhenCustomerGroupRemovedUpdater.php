<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\Update;

use Doctrine\DBAL\Connection;

class DiscountWhenCustomerGroupRemovedUpdater
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function handle(int $groupId): void
    {
        $cartRuleIds = $this->getCartRuleIdsForGroup($groupId);
        if (empty($cartRuleIds)) {
            return;
        }

        foreach ($cartRuleIds as $cartRuleId) {
            $groupCount = $this->countGroupsForCartRule($cartRuleId);
            if ($groupCount <= 1) {
                $this->disableAndApplyToAllCustomers($cartRuleId);
            } else {
                $this->removeGroupFromCartRule($cartRuleId, $groupId);
            }
        }
    }

    public function handleCustomerGroupsFeatureDisabled(): void
    {
        $cartRuleIds = $this->getCartRuleIdsWithGroupRestriction();
        foreach ($cartRuleIds as $cartRuleId) {
            $this->disableAndApplyToAllCustomers($cartRuleId);
        }
    }

    /**
     * @return int[]
     */
    private function getCartRuleIdsWithGroupRestriction(): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('cr.id_cart_rule')
            ->from($this->dbPrefix . 'cart_rule', 'cr')
            ->where('cr.group_restriction = 1')
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id_cart_rule'], $result);
    }

    /**
     * @return int[]
     */
    private function getCartRuleIdsForGroup(int $groupId): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select('crg.id_cart_rule')
            ->from($this->dbPrefix . 'cart_rule_group', 'crg')
            ->where('crg.id_group = :groupId')
            ->setParameter('groupId', $groupId)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id_cart_rule'], $result);
    }

    private function countGroupsForCartRule(int $cartRuleId): int
    {
        return (int) $this->connection->createQueryBuilder()
            ->select('COUNT(crg.id_group)')
            ->from($this->dbPrefix . 'cart_rule_group', 'crg')
            ->where('crg.id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId)
            ->executeQuery()
            ->fetchOne();
    }

    private function disableAndApplyToAllCustomers(int $cartRuleId): void
    {
        $this->connection->createQueryBuilder()
            ->update($this->dbPrefix . 'cart_rule')
            ->set('group_restriction', '0')
            ->set('active', '0')
            ->where('id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId)
            ->executeStatement();

        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_group')
            ->where('id_cart_rule = :cartRuleId')
            ->setParameter('cartRuleId', $cartRuleId)
            ->executeStatement();
    }

    private function removeGroupFromCartRule(int $cartRuleId, int $groupId): void
    {
        $this->connection->createQueryBuilder()
            ->delete($this->dbPrefix . 'cart_rule_group')
            ->where('id_cart_rule = :cartRuleId')
            ->andWhere('id_group = :groupId')
            ->setParameter('cartRuleId', $cartRuleId)
            ->setParameter('groupId', $groupId)
            ->executeStatement();
    }
}
