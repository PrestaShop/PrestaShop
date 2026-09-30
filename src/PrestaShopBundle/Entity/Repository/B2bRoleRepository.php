<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use PrestaShopBundle\Entity\B2B\B2bRole;

/**
 * @extends EntityRepository<B2bRole>
 *
 * @experimental
 */
class B2bRoleRepository extends EntityRepository
{
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
