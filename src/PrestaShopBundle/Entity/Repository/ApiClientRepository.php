<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
