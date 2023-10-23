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

namespace PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command;

use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\ValueObject\ApiAccessId;

class EditApiAccessCommand
{
    private ApiAccessId $apiAccessId;

    private ?string $apiClientId = null;

    private ?string $clientName = null;

    private ?bool $enabled = null;

    private ?string $description = null;

    private ?array $scopes = null;

    public function __construct(int $apiAccessId)
    {
        $this->apiAccessId = new ApiAccessId($apiAccessId);
    }

    public function getApiAccessId(): ApiAccessId
    {
        return $this->apiAccessId;
    }

    public function getApiClientId(): ?string
    {
        return $this->apiClientId;
    }

    public function setApiClientId(string $apiClientId): self
    {
        $this->apiClientId = $apiClientId;

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
        if ((count($scopes) !== count(array_filter($scopes, 'is_string')))) {
            throw new ApiAccessConstraintException('Expected list of non empty string for scopes', ApiAccessConstraintException::INVALID_SCOPES);
        }
        $this->scopes = $scopes;

        return $this;
    }
}
