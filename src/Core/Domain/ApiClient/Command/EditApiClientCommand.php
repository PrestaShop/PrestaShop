<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\Command;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\ApiClientId;

class EditApiClientCommand
{
    private ApiClientId $apiClientId;

    private ?string $clientId = null;

    private ?string $clientName = null;

    private ?bool $enabled = null;

    private ?string $description = null;

    private ?array $scopes = null;

    private ?int $lifetime = null;

    public function __construct(int $apiClientId)
    {
        $this->apiClientId = new ApiClientId($apiClientId);
    }

    public function getApiClientId(): ApiClientId
    {
        return $this->apiClientId;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): self
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    public function setScopes(?array $scopes): self
    {
        if (count($scopes) !== count(array_filter($scopes, 'is_string'))) {
            throw new ApiClientConstraintException('Expected list of non empty string for scopes', ApiClientConstraintException::INVALID_SCOPES);
        }
        $this->scopes = $scopes;

        return $this;
    }

    /** Returns the lifetime in milliseconds. Default is 3600. */
    public function getLifetime(): ?int
    {
        return $this->lifetime;
    }

    public function setLifetime(?int $lifetime): self
    {
        $this->lifetime = $lifetime;

        return $this;
    }
}
