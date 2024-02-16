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

namespace PrestaShop\PrestaShop\Adapter\ApiAccess\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\GenerateApiAccessSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\CommandHandler\GenerateApiAccessSecretHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\CannotGenerateSecretApiAccessException;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

#[AsCommandHandler]
class GenerateApiAccessSecretHandler implements GenerateApiAccessSecretHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function handle(GenerateApiAccessSecretCommand $command): string
    {
        try {
            $apiClient = $this->repository->getById($command->getApiAccessId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiAccessNotFoundException(sprintf('Could not find Api access with ID %s', $command->getApiAccessId()->getValue()), 0, $e);
        }

        try {
            $secret = RandomString::generate();
            $apiClient->setClientSecret($this->passwordHasher->hash($secret));
            $this->repository->save($apiClient);

            return $secret;
        } catch (ORMException $e) {
            throw new CannotGenerateSecretApiAccessException('Could not generate new token Api access', 0, $e);
        }
    }
}
