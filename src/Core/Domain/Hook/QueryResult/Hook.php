<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult;

class Hook
{
    public function __construct(
        public readonly int $id,
        public readonly bool $active,
        public readonly string $name,
        public readonly string $title,
        public readonly string $description
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
