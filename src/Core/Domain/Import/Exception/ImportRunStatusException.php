<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown on an illegal import run status transition (e.g. running a cancelled run).
 */
final class ImportRunStatusException extends ImportException
{
}
