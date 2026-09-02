<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

/**
 * Immutable DTO Class representing the employee accessible via the EmployeeContext
 *
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/33
 */
class Employee
{
    public function __construct(
        protected int $id,
        protected int $profileId,
        protected int $languageId,
        protected string $firstName,
        protected string $lastName,
        protected string $email,
        protected string $password,
        protected string $imageUrl,
        protected int $defaultTabId,
        protected int $defaultShopId,
        protected array $associatedShopIds,
        protected array $associatedShopGroupIds,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProfileId(): int
    {
        return $this->profileId;
    }

    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getDefaultTabId(): int
    {
        return $this->defaultTabId;
    }

    public function getAssociatedShopIds(): array
    {
        return $this->associatedShopIds;
    }

    public function getDefaultShopId(): int
    {
        return $this->defaultShopId;
    }

    public function getAssociatedShopGroupIds(): array
    {
        return $this->associatedShopGroupIds;
    }
}
