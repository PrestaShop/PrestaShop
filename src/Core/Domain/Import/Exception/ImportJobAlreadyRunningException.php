<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when a batch is requested for a job that already has one running.
 *
 * Fails fast rather than queueing, so the job's offset cannot advance twice in parallel.
 */
final class ImportJobAlreadyRunningException extends ImportException
{
}
