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

namespace PrestaShop\PrestaShop\Adapter\ApiClient\CommandHandler;

use Doctrine\ORM\Exception\ORMException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler\AddApiClientCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotAddApiClientException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShopBundle\Entity\ApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommandHandler]
class AddApiClientHandler implements AddApiClientCommandHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
        private readonly ValidatorInterface $validator,
        private readonly PasswordHasherInterface $passwordHasher
    ) {
    }

    public function handle(AddApiClientCommand $command): CreatedApiClient
    {
        $apiClient = new ApiClient();
        $apiClient->setClientId($command->getClientId());
        $apiClient->setClientName($command->getClientName());
        $secret = RandomString::generate();
        $apiClient->setClientSecret($this->passwordHasher->hash($secret));
        $apiClient->setEnabled($command->isEnabled());
        $apiClient->setDescription($command->getDescription());
        $apiClient->setScopes($command->getScopes());
        $apiClient->setLifetime($command->getLifetime());

        $errors = $this->validator->validate($apiClient);

        if (count($errors) > 0) {
            throw ApiClientConstraintException::buildFromPropertyPath(
                $errors->get(0)->getPropertyPath(),
                $errors->get(0)->getMessage(),
                $errors->get(0)->getMessageTemplate()
            );
        }

        try {
            $apiClientId = $this->repository->save($apiClient);
        } catch (ORMException $e) {
            throw new CannotAddApiClientException('Could not add Api client', 0, $e);
        }

        return new CreatedApiClient($apiClientId, $secret);
    }
}
