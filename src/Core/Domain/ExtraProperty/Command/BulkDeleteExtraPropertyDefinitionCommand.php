<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

/**
 * Deletes several core extra property definitions in bulk, optionally dropping their physical
 * SQL columns. Module-owned definitions among the given ids are skipped (aggregated as a
 * BulkExtraPropertyException) rather than stopping the whole batch.
 */
class BulkDeleteExtraPropertyDefinitionCommand
{
    /**
     * @param int[] $ids
     * @param bool $dropColumn When true, the physical column in {entity}_extra table is also dropped
     */
    public function __construct(
        protected readonly array $ids,
        protected readonly bool $dropColumn = false,
    ) {
    }

    /**
     * @return int[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    public function shouldDropColumn(): bool
    {
        return $this->dropColumn;
    }
}
