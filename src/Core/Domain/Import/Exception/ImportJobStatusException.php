<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown on an illegal status transition, e.g. continuing a job that was cancelled or has
 * already finished.
 */
final class ImportJobStatusException extends ImportException
{
}
