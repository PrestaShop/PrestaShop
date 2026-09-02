<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

class ApiClient
{
    public function __construct(
        private int $id,
        private string $clientId,
        private array $scopes,
        private ?string $externalIssuer,
        private int $shopId,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes);
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getExternalIssuer(): ?string
    {
        return $this->externalIssuer;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }
}
