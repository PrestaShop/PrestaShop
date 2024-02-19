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

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\ApiClient as ApiClientEntity;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;

class ApiClientContextBuilder
{
    private string $clientId;
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
                clientId: $apiClient->getClientId(),
                scopes: $apiClient->getScopes(),
                shopId: (int) $defaultShopId
            );
        }

        return new ApiClientContext($apiClientDTO);
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    private function getApiClient(): ?ApiClientEntity
    {
        if (!$this->apiClient && !empty($this->clientId)) {
            $this->apiClient = $this->apiClientRepository->getByClientId($this->clientId);
        }

        return $this->apiClient;
    }
}
