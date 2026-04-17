<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ApiClient\QueryHandler;

use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryHandler\GetApiClientForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;

#[AsQueryHandler]
class GetApiClientForEditingHandler implements GetApiClientForEditingHandlerInterface
{
    public function __construct(private readonly ApiClientRepository $repository)
    {
    }

    public function handle(GetApiClientForEditing $query): EditableApiClient
    {
        try {
            $apiClient = $this->repository->getById($query->getApiClientId()->getValue());
        } catch (NoResultException $e) {
            throw new ApiClientNotFoundException(sprintf('Could not find Api client %s', $query->getApiClientId()->getValue()), 0, $e);
        }

        return new EditableApiClient(
            $apiClient->getId(),
            $apiClient->getClientId(),
            $apiClient->getClientName(),
            $apiClient->isEnabled(),
            $apiClient->getDescription(),
            $apiClient->getScopes(),
            $apiClient->getLifetime(),
            $apiClient->getExternalIssuer(),
        );
    }
}
