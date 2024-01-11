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
