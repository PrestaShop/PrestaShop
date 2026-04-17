<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Command;

class AddTagCommand
{
    public function __construct(
        private readonly string $name,
        private readonly int $languageId,
        private readonly array $productIds = []
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLanguageId(): ?int
    {
        return $this->languageId;
    }

    /**
     * @return int[]
     */
    public function getProductIds(): array
    {
        return $this->productIds;
    }
}
