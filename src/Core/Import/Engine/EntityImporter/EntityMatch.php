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
     * @param int|null $entityId existing entity to update, null to create
     * @param string|null $matchedBy MATCHED_BY_* constant when updating
     * @param int|null $forcedId id to force on creation (force IDs option)
     */
    public function __construct(
        public readonly ?int $entityId,
        public readonly ?string $matchedBy = null,
        public readonly ?int $forcedId = null,
    ) {
    }

    public function isUpdate(): bool
    {
        return null !== $this->entityId;
    }
}
