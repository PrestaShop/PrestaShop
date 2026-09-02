<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Security\OAuth2;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtTokenUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected array $roles = [];

    public function __construct(
        protected readonly string $userId,
        protected readonly array $scopes,
        protected readonly ?string $externalIssuer = null,
    ) {
        $this->convertScopesToRoles();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        return;
    }

    public function getUserIdentifier(): string
    {
        return $this->userId;
    }

    public function getExternalIssuer(): ?string
    {
        return $this->externalIssuer;
    }

    protected function convertScopesToRoles(): void
    {
        foreach ($this->scopes as $scope) {
            $this->roles[] = 'ROLE_' . strtoupper($scope);
        }
    }
}
