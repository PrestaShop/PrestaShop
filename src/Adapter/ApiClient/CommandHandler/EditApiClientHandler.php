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
use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\EditApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\CommandHandler\EditApiClientCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\CannotUpdateApiClientException;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommandHandler]
class EditApiClientHandler implements EditApiClientCommandHandlerInterface
{
    public function __construct(
        private readonly ApiClientRepository $repository,
        private readonly ValidatorInterface $validator
    ) {
    }

    public function handle(EditApiClientCommand $command): void
    {
        try {
            $apiClient = $this->repository->getById($command->getApiClientId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiClientNotFoundException(sprintf('Could not find Api client %s', $command->getClientId()), 0, $e);
        }

        if (!is_null($command->getClientId())) {
            $apiClient->setClientId($command->getClientId());
        }

        if (!is_null($command->getClientName())) {
            $apiClient->setClientName($command->getClientName());
        }

        if (!is_null($command->isEnabled())) {
            $apiClient->setEnabled($command->isEnabled());
        }

        if (!is_null($command->getDescription())) {
            $apiClient->setDescription($command->getDescription());
        }

        if (!is_null($command->getScopes())) {
            $apiClient->setScopes($command->getScopes());
        }

        if (!is_null($command->getLifetime())) {
            $apiClient->setLifetime($command->getLifetime());
        }

        $errors = $this->validator->validate($apiClient);

        if (count($errors) > 0) {
            throw ApiClientConstraintException::buildFromPropertyPath(
                $errors->get(0)->getPropertyPath(),
                $errors->get(0)->getMessage(),
                $errors->get(0)->getMessageTemplate()
            );
        }

        try {
            $this->repository->save($apiClient);
        } catch (ORMException $e) {
            throw new CannotUpdateApiClientException('Could not update Api client', 0, $e);
        }
    }
}
