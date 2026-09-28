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
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotDeleteCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
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
     * Returns the curated rules of a shop as a flat list, for the policy builder to merge.
     *
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
}
