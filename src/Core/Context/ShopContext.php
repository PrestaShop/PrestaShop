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

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This context service gives access to all contextual data related to shop.
 */
class ShopContext
{
    public function __construct(
        protected readonly ShopConstraint $shopConstraint,
        protected int $id,
        protected string $name,
        protected int $shopGroupId,
        protected int $categoryId,
        protected string $themeName,
        protected string $color,
        protected string $physicalUri,
        protected string $virtualUri,
        protected string $domain,
        protected string $domainSSL,
        protected bool $active,
        protected bool $secured,
    ) {
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShopGroupId(): int
    {
        return $this->shopGroupId;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getPhysicalUri(): string
    {
        return $this->physicalUri;
    }

    public function getVirtualUri(): string
    {
        return $this->virtualUri;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getDomainSSL(): string
    {
        return $this->domainSSL;
    }

    public function getBaseURI(): string
    {
        return $this->physicalUri . $this->virtualUri;
    }

    public function getBaseURL(): string
    {
        if ($this->secured) {
            $url = 'https://' . $this->domainSSL;
        } else {
            $url = 'http://' . $this->domain;
        }

        return $url . $this->getBaseURI();
    }
}
