<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
