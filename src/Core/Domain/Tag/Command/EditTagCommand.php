<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tag\Command;

use PrestaShop\PrestaShop\Core\Domain\Tag\ValueObject\TagId;

class EditTagCommand
{
    private TagId $tagId;

    private ?string $name = null;

    private ?int $languageId = null;

    private ?array $productIds = null;

    public function __construct(int $tagId)
    {
        $this->tagId = new TagId($tagId);
    }

    public function getTagId(): TagId
    {
        return $this->tagId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLanguageId(): ?int
    {
        return $this->languageId;
    }

    public function setLanguageId(int $languageId): self
    {
        $this->languageId = $languageId;

        return $this;
    }

    public function getProductIds(): ?array
    {
        return $this->productIds;
    }

    public function setProductIds(?array $productIds): self
    {
        $this->productIds = $productIds;

        return $this;
    }
}
