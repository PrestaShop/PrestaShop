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
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationRepositoryInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\ValueObject\ApplicationId;

/**
 * @experimental
 */
class AuthorizedApplicationRepository extends EntityRepository implements AuthorizedApplicationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(AuthorizedApplicationInterface $application): void
    {
        $this->getEntityManager()->persist($application);
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function update(AuthorizedApplicationInterface $application): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getById(ApplicationId $applicationId): AuthorizedApplicationInterface
    {
        $application = $this->find($applicationId->getValue());
        if ($application === null) {
            throw new ApplicationNotFoundException(sprintf('Application with id "%d" was not found.', $applicationId->getValue()));
        }

        return $application;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName(string $name): AuthorizedApplicationInterface
    {
        $application = $this->findOneBy(['name' => $name]);
        if ($application === null) {
            throw new ApplicationNotFoundException(sprintf('Application with name "%d" was not found.', $name));
        }

        return $application;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AuthorizedApplicationInterface $application): void
    {
        $this->getEntityManager()->remove($application);
        $this->getEntityManager()->flush();
    }
}
