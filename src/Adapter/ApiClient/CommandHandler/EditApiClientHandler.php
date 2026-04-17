<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
