<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult;

/**
 * Represents a hook that a module can be hooked to.
 * Returned as a list by GetPossibleHooksForModule.
 */
class HookableInfo
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $title,
        public readonly bool $registered
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isRegistered(): bool
    {
        return $this->registered;
    }
}
