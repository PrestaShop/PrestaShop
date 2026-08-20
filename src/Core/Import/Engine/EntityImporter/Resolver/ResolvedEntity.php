<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Resolver;

/**
 * Result of a resolve-or-create resolution: it always ends with an id (the
 * entity was created when nothing matched). Pure data — whether an ambiguous
 * match warns, and with which wording, is the caller's business (see the
 * ambiguity policy in the Import PLAN.md).
 */
class ResolvedEntity
{
    /**
     * @param bool $wasCreated the entity did not exist and was auto-created
     * @param int $matchCount >1: the searched name matched several entities and the lowest id was used
     */
    public function __construct(
        public readonly int $id,
        public readonly bool $wasCreated = false,
        public readonly int $matchCount = 1,
    ) {
    }

    public function isAmbiguous(): bool
    {
        return $this->matchCount > 1;
    }
}
