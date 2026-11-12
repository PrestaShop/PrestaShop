<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\B2B\CustomerB2b;

/**
 * @extends EntityRepository<CustomerB2b>
 *
 * @experimental
 */
class CustomerB2bRepository extends EntityRepository
{
    public function save(CustomerB2b $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function findByCustomerId(int $customerId): ?CustomerB2b
    {
        return $this->findOneBy(['idCustomer' => $customerId]);
    }
}
