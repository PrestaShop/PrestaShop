<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

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

    /** Counts a scope's allowed sources that weaken the policy, using the same rule as the grid's is_weakening. */
    public function countWeakeningRulesByShop(CspContext $context, int $shopId): int
    {
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(1)')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere('context = :context')
            ->andWhere(CspWeakeningExpression::sql() . ' = 1')
            ->setParameter('shopId', $shopId)
            ->setParameter('context', $context->value);
        CspWeakeningExpression::bindParameters($qb);

        return (int) $qb
            ->executeQuery()
            ->fetchOne();
    }
}
