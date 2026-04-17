<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\ApiClient as ApiClientEntity;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;

class ApiClientContextBuilder
{
    private string $clientId;
    private ?string $externalIssuer = null;
    private ?ApiClientEntity $apiClient = null;

    public function __construct(
        private ApiClientRepository $apiClientRepository,
        private readonly ShopConfigurationInterface $configuration
    ) {
    }

    public function build(): ApiClientContext
    {
        $apiClientDTO = null;
        $apiClient = $this->getApiClient();
        if ($apiClient) {
            // Authorized shop should be associated to the client but for no we use the default one
            $defaultShopId = $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops());
            $apiClientDTO = new ApiClient(
                id: $apiClient->getId(),
                clientId: $apiClient->getClientId(),
                scopes: $apiClient->getScopes(),
                externalIssuer: $apiClient->getExternalIssuer(),
                shopId: (int) $defaultShopId,
            );
        }

        return new ApiClientContext($apiClientDTO);
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setExternalIssuer(?string $externalIssuer): self
    {
        $this->externalIssuer = $externalIssuer;

        return $this;
    }

    private function getApiClient(): ?ApiClientEntity
    {
        if (!$this->apiClient && !empty($this->clientId)) {
            $this->apiClient = $this->apiClientRepository->getByClientId($this->clientId, $this->externalIssuer);
        }

        return $this->apiClient;
    }
}
