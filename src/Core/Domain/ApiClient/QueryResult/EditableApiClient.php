<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult;

class EditableApiClient
{
    public function __construct(
        private readonly int $apiClientId,
        private readonly string $clientId,
        private readonly string $clientName,
        private readonly bool $enabled,
        private readonly string $description,
        private readonly array $scopes,
        private readonly int $lifetime,
        private readonly ?string $externalIssuer,
    ) {
    }

    public function getApiClientId(): int
    {
        return $this->apiClientId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function getExternalIssuer(): ?string
    {
        return $this->externalIssuer;
    }
}
