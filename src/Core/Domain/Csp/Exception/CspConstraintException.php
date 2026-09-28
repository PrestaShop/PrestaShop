<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Exception;

/**
 * Thrown when a single CSP value fails format validation in its value object.
 */
class CspConstraintException extends CspException
{
    public const INVALID_DIRECTIVE = 1;
    public const INVALID_SOURCE = 2;
}
