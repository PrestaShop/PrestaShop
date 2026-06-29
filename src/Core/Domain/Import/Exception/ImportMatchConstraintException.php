<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\Exception;

/**
 * Thrown when an import match value does not pass domain constraints.
 */
final class ImportMatchConstraintException extends ImportException
{
    public const INVALID_ID = 1;
    public const INVALID_NAME = 2;
}
