<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ApiClient\Command;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;

class AddApiClientCommand
{
    public function __construct(
        private readonly string $clientName,
        private readonly string $clientId,
        private readonly bool $enabled,
        private readonly string $description,
        private readonly int $lifetime,
        private readonly array $scopes = []
    ) {
        if (count($scopes) !== count(array_filter($scopes, 'is_string'))) {
            throw new ApiClientConstraintException('Expected list of non empty string for scopes', ApiClientConstraintException::INVALID_SCOPES);
        }
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getDescription(): ?string
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

    public function getLifetime(): ?int
    {
        return $this->lifetime;
    }
}
