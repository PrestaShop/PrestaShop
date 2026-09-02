<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryResult;

/**
 * Immutable DTO carrying quick access data for the edit form.
 * All properties use scalar types (int, string, bool, array) — no VOs — per CQRS QueryResult convention.
 */
class EditableQuickAccess
{
    /** @param array<int, string> $localizedNames Lang-ID-keyed name translations */
    public function __construct(
        private readonly int $quickAccessId,
        private readonly array $localizedNames,
        private readonly string $link,
        private readonly bool $newWindow,
    ) {
    }

    public function getQuickAccessId(): int
    {
        return $this->quickAccessId;
    }

    /** @return array<int, string> */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function isNewWindow(): bool
    {
        return $this->newWindow;
    }
}
