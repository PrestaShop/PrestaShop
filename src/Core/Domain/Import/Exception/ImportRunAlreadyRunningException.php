<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when a batch is requested for an import run that already has a batch in progress.
 *
 * Batches of the same run are serialized by a per-run lock; a concurrent second batch fails fast
 * (it does not queue) so the run's offset cannot be advanced twice in parallel.
 */
final class ImportRunAlreadyRunningException extends ImportException
{
}
