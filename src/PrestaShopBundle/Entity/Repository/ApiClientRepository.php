<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use PrestaShopBundle\Entity\ApiClient;

class ApiClientRepository extends EntityRepository
{
    /**
     * @param int $apiClientId
     *
     * @return ApiClient
     *
     * @throws NoResultException
     */
    public function getById(int $apiClientId): ApiClient
    {
        $apiClient = $this->findOneBy(['id' => $apiClientId]);

        if (null === $apiClient) {
            throw new NoResultException();
        }

        return $apiClient;
    }

    /**
     * @param string $clientId
     * @param string|null $externalIssuer
     *
     * @return ApiClient
     *
     * @throws NoResultException
     */
    public function getByClientId(string $clientId, ?string $externalIssuer = null): ApiClient
    {
        $apiClient = $this->findOneBy(['clientId' => $clientId, 'externalIssuer' => $externalIssuer]);

        if (null === $apiClient) {
            throw new NoResultException();
        }

        return $apiClient;
    }

    public function delete(ApiClient $apiClient): void
    {
        $this->getEntityManager()->remove($apiClient);
        $this->getEntityManager()->flush();
    }

    public function save(ApiClient $apiClient): int
    {
        $this->getEntityManager()->persist($apiClient);
        $this->getEntityManager()->flush();

        return $apiClient->getId();
    }
}
