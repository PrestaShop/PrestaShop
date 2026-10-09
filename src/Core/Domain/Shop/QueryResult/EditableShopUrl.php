<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

final class EditableShopUrl
{
    public function __construct(
        private readonly int $shopUrlId,
        private readonly int $shopId,
        private readonly string $domain,
        private readonly string $domainSsl,
        private readonly string $physicalUri,
        private readonly string $virtualUri,
        private readonly bool $main,
        private readonly bool $active,
    ) {
    }

    public function getShopUrlId(): int
    {
        return $this->shopUrlId;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getDomainSsl(): string
    {
        return $this->domainSsl;
    }

    public function getPhysicalUri(): string
    {
        return $this->physicalUri;
    }

    public function getVirtualUri(): string
    {
        return $this->virtualUri;
    }

    public function isMain(): bool
    {
        return $this->main;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
