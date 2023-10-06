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

class AddApiAccessCommand
{
    public function __construct(
        private readonly string $clientName,
        private readonly string $apiClientId,
        private readonly bool $enabled,
        private readonly string $description,
        private readonly array $scopes = []
    ) {
        foreach ($scopes as $scope) {
            if (empty($scope) || !is_string($scope)) {
                throw new ApiAccessConstraintException('Expected list of non empty string for scopes', ApiAccessConstraintException::INVALID_SCOPES);
            }
        }
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function getApiClientId(): ?string
    {
        return $this->apiClientId;
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
}
