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
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class CartRuleRepository extends AbstractObjectModelRepository
{
    public function __construct(
        protected CartRuleValidator $cartRuleValidator,
        protected Connection $connection,
        protected string $dbPrefix
    ) {
    }

    public function add(CartRule $cartRule): CartRule
    {
        $this->cartRuleValidator->validate($cartRule);
        $this->addObjectModel($cartRule, CannotAddCartRuleException::class);

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

        $this->removeRestrictedCartRules($cartRuleId);
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
}
