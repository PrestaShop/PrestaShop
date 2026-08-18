<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

/**
 * Update-vs-create decision for one imported row, shared by every entity
 * importer (each importer owns its resolution logic, e.g.
 * ProductIdentityResolver).
 */
class EntityMatch
{
    public const MATCHED_BY_REFERENCE = 'reference';
    public const MATCHED_BY_ID = 'id';

    /**
     * How many entities matched the identity lookup: 0 on a miss, 1 on a clean
     * match, more when the identity column (e.g. product.reference, which has no
     * unique constraint) matches several entities — entityId then carries the
     * LOWEST matching id, and the caller decides whether the ambiguity warns or
     * fails (see the ambiguity policy in the Import PLAN.md, decision 22).
     */
    public readonly int $matchCount;

    /**
     * @param int|null $entityId existing entity to update, null to create
     * @param string|null $matchedBy MATCHED_BY_* constant when updating
     * @param int|null $forcedId id to force on creation (force IDs option)
     * @param int|null $matchCount defaults to 1 for a match and 0 for a miss
     */
    public function __construct(
        public readonly ?int $entityId,
        public readonly ?string $matchedBy = null,
        public readonly ?int $forcedId = null,
        ?int $matchCount = null,
    ) {
        $this->matchCount = $matchCount ?? (null !== $entityId ? 1 : 0);
    }

    public function isUpdate(): bool
    {
        return null !== $this->entityId;
    }
}
