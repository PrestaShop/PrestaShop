<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

/**
 * Thrown when an extra property constraint is malformed, carries an invalid argument, or cannot be
 * serialized safely (unsupported class, object graph or executable option).
 *
 * Distinct from UnknownExtraPropertyConstraintException, which covers an unrecognized name.
 */
class InvalidExtraPropertyConstraintException extends ExtraPropertyException
{
}
