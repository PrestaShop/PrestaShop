<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Discount\QueryResult;

class DiscountType
{
    /**
     * @param int $discountTypeId
     * @param string $type
     * @param array<int, string> $localizedNames indexed by language ID
     * @param array<int, string> $localizedDescriptions indexed by language ID
     * @param bool $isCore
     * @param bool $enabled
     */
    public function __construct(
        private readonly int $discountTypeId,
        private readonly string $type,
        private readonly array $localizedNames,
        private readonly array $localizedDescriptions,
        private readonly bool $isCore = false,
        private readonly bool $enabled = true
    ) {
    }

    public function getDiscountTypeId(): int
    {
        return $this->discountTypeId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    public function isCore(): bool
    {
        return $this->isCore;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
