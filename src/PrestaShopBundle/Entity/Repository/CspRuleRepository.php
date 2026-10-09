<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Csp\CspWeakeningExpression;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotDeleteCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\CspRule;

/**
 * @extends EntityRepository<CspRule>
 */
class CspRuleRepository extends EntityRepository
{
    public function add(CspRule $rule): int
    {
        try {
            $this->getEntityManager()->persist($rule);
            $this->getEntityManager()->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new CannotAddCspRuleException('Failed to add the CSP rule: it already exists.', 0, $e);
        } catch (DBALException $e) {
            throw new CannotAddCspRuleException('Failed to add the CSP rule.', 0, $e);
        }

        return $rule->getId();
    }

    /**
     * @throws CspRuleNotFoundException
     */
    public function getById(int $cspRuleId): CspRule
    {
        $rule = $this->find($cspRuleId);

        if (null === $rule) {
            throw new CspRuleNotFoundException(sprintf('CSP rule #%d was not found.', $cspRuleId));
        }

        return $rule;
    }

    public function findOneByShopDirectiveSource(CspContext $context, int $shopId, string $directive, string $source): ?CspRule
    {
        return $this->findOneBy(['shopId' => $shopId, 'context' => $context->value, 'directive' => $directive, 'source' => $source]);
    }

    public function delete(CspRule $rule): void
    {
        try {
            $this->getEntityManager()->remove($rule);
            $this->getEntityManager()->flush();
        } catch (DBALException $e) {
            throw new CannotDeleteCspRuleException(sprintf('Failed to delete the CSP rule #%d.', $rule->getId()), 0, $e);
        }
    }

    /**
     * @return list<array{directive: string, source: string}>
     */
    public function getRulesByShop(CspContext $context, int $shopId): array
    {
        /** @var list<array{directive: string, source: string}> $rows */
        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('r.directive', 'r.source')
            ->from($this->getClassMetadata()->getTableName(), 'r')
            ->where('r.id_shop = :shopId')
            ->andWhere('r.context = :context')
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }

    /** Whether the scope has any curated rule; for the baseline check, which only needs existence. */
    public function existsByShop(CspContext $context, int $shopId): bool
    {
        return (bool) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value)
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Whether any of the given shops has an allowed source that weakens the policy, in one query, using
     * the same rule as the grid's is_weakening (keyword/wildcard, wildcard host, or broadening scheme on
     * script-/style-src). Drives the page's weakening warning across an all-shops scope without a query
     * per shop.
     *
     * @param list<int> $shopIds
     */
    public function hasWeakeningRuleForAnyShop(CspContext $context, array $shopIds): bool
    {
        if ($shopIds === []) {
            return false;
        }

        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop IN (:shopIds)')
            ->andWhere('context = :context')
            ->andWhere(CspWeakeningExpression::sql() . ' = 1')
            ->setMaxResults(1)
            ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
            ->setParameter('context', $context->value);
        CspWeakeningExpression::bindParameters($qb);

        return false !== $qb->executeQuery()->fetchOne();
    }
}
