<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\ValueObject;

/**
 * What a purge collected.
 *
 * The two counts do not have to match: a working file is also removed when its job row was never
 * written, and a job row is also removed when its file was already gone.
 */
final class ImportJobPurgeSummary
{
    public function __construct(
        private readonly int $purgedJobCount,
        private readonly int $removedWorkingFileCount,
    ) {
    }

    public function getPurgedJobCount(): int
    {
        return $this->purgedJobCount;
    }

    public function getRemovedWorkingFileCount(): int
    {
        return $this->removedWorkingFileCount;
    }
}
