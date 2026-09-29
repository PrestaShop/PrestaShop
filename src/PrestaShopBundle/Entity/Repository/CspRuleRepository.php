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
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotDeleteCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
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

    public function findOneByShopDirectiveSource(int $shopId, string $directive, string $source): ?CspRule
    {
        return $this->findOneBy(['shopId' => $shopId, 'directive' => $directive, 'source' => $source]);
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
    public function getRulesByShop(int $shopId): array
    {
        /** @var list<array{directive: string, source: string}> $rows */
        $rows = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('r.directive', 'r.source')
            ->from($this->getClassMetadata()->getTableName(), 'r')
            ->where('r.id_shop = :shopId')
            ->setParameter('shopId', $shopId)
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }

    /** Whether the shop has any curated rule; for the baseline check, which only needs existence. */
    public function existsByShop(int $shopId): bool
    {
        return (bool) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('1')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->setMaxResults(1)
            ->setParameter('shopId', $shopId)
            ->executeQuery()
            ->fetchOne();
    }

    /** Counts a shop's allowed sources that weaken the policy, using the same rule as the grid's is_weakening. */
    public function countWeakeningRulesByShop(int $shopId): int
    {
        return (int) $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(1)')
            ->from($this->getClassMetadata()->getTableName())
            ->where('id_shop = :shopId')
            ->andWhere("source IN (:weakeningSources) OR source LIKE '%*%' OR (directive IN (:scriptStyleDirectives) AND source IN (:broadeningSchemes))")
            ->setParameter('shopId', $shopId)
            ->setParameter('weakeningSources', CspSource::WEAKENING_KEYWORDS, ArrayParameterType::STRING)
            ->setParameter('scriptStyleDirectives', [CspDirective::SCRIPT_SRC->value, CspDirective::STYLE_SRC->value], ArrayParameterType::STRING)
            ->setParameter('broadeningSchemes', CspSource::BROADENING_SCHEMES, ArrayParameterType::STRING)
            ->executeQuery()
            ->fetchOne();
    }
}
