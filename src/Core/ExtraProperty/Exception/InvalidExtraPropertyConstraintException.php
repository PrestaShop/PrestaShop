<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

/**
 * Thrown when a BO "Validation" textarea entry is recognized by name but malformed or carries an
 * invalid argument (e.g. a required value is missing, or a value is supplied to a constraint that
 * takes none).
 *
 * Distinct from UnknownExtraPropertyConstraintException, which covers an unrecognized name.
 */
class InvalidExtraPropertyConstraintException extends ExtraPropertyException
{
}
