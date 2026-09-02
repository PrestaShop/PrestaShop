<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;

class ApiClientFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus
    ) {
    }

    public function getData($id)
    {
        /** @var EditableApiClient $apiAccess */
        $apiAccess = $this->queryBus->handle(new GetApiClientForEditing((int) $id));

        return [
            'client_id' => $apiAccess->getClientId(),
            'client_name' => $apiAccess->getClientName(),
            'description' => $apiAccess->getDescription(),
            'enabled' => $apiAccess->isEnabled(),
            'scopes' => $apiAccess->getScopes(),
            'lifetime' => $apiAccess->getLifetime(),
            'external_issuer' => $apiAccess->getExternalIssuer(),
        ];
    }

    public function getDefaultData()
    {
        return [
            'lifetime' => 3600,
        ];
    }
}
