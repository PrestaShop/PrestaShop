<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\B2B\BusinessEntity;

class BusinessEntityRepository extends EntityRepository
{
    public function save(BusinessEntity $businessEntity): int
    {
        $this->getEntityManager()->persist($businessEntity);
        $this->getEntityManager()->flush();

        return $businessEntity->getId();
    }
}
