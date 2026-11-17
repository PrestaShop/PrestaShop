<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\WithdrawalRequest;

/**
 * Loads and persists {@see WithdrawalRequest}; handlers never touch the EntityManager.
 *
 * @extends EntityRepository<WithdrawalRequest>
 */
class WithdrawalRequestRepository extends EntityRepository
{
    public function save(WithdrawalRequest $withdrawalRequest): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($withdrawalRequest);
        $entityManager->flush();
    }

    public function findById(int $withdrawalRequestId): ?WithdrawalRequest
    {
        return $this->find($withdrawalRequestId);
    }

    public function findOneByOrderId(int $orderId): ?WithdrawalRequest
    {
        return $this->findOneBy(['orderId' => $orderId]);
    }
}
