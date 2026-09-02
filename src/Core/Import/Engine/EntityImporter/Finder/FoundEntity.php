<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

/**
 * The single result type of every finder: pure data, no interpretation. None
 * of the columns the import matches on carries a unique constraint, so a
 * lookup can return several matches — each carrying HOW it matched, so one
 * result can mix strategies (e.g. an accessory target matching a product id
 * AND other products' references). The CALLER decides whether ambiguity warns
 * or fails, and words any message (see the ambiguity policy in the Import
 * PLAN.md).
 *
 * Caller contract on the row-identity path: check isAmbiguous() and
 * foundOutsideShopScope BEFORE using first() as an update target — updating
 * an arbitrary one of several matches is destructive (pinned by the
 * duplicate-reference integration test).
 */
class FoundEntity
{
    public const MATCHED_BY_ID = 'id';
    public const MATCHED_BY_NAME = 'name';
    public const MATCHED_BY_REFERENCE = 'reference';

    /**
     * @param list<array{id: int, matchedBy: string}> $matches every match — strongest
     *                                                         strategy first (an id match wins over reference matches), lowest id
     *                                                         first within a strategy. NOT deduplicated across strategies: the same
     *                                                         entity matched by id AND by reference appears twice, which is exactly
     *                                                         the collision information callers warn about
     * @param int|null $forcedId id to force on creation (force IDs run option — generic:
     *                           the legacy import consults forceIDs for every entity type);
     *                           only ever set on a miss
     * @param bool $foundOutsideShopScope the term matches only OUTSIDE the run's shop
     *                                    scope (match_ref today) — creating would duplicate
     *                                    it, so callers fail the row
     */
    public function __construct(
        public readonly array $matches,
        public readonly ?int $forcedId = null,
        public readonly bool $foundOutsideShopScope = false,
    ) {
    }

    public function first(): ?int
    {
        return $this->matches[0]['id'] ?? null;
    }

    public function firstMatchedBy(): ?string
    {
        return $this->matches[0]['matchedBy'] ?? null;
    }

    public function count(): int
    {
        return count($this->matches);
    }

    public function isAmbiguous(): bool
    {
        return $this->count() > 1;
    }
}
