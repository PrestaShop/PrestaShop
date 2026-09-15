<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PrestaShopBundle\Entity\B2B\B2bRole;

/**
 * @extends ServiceEntityRepository<B2bRole>
 *
 * @experimental
 */
class B2bRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, B2bRole::class);
    }

    public function createByLanguageIdQueryBuilder(int $languageId): QueryBuilder
    {
        return $this
            ->createQueryBuilder('r')
            ->addSelect('rt')
            ->leftJoin('r.translations', 'rt', Join::WITH, 'rt.language = :languageId')
            ->orderBy('r.id', 'ASC')
            ->setParameter('languageId', $languageId);
    }
}
