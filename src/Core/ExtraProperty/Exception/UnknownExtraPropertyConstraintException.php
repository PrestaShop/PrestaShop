<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

/**
 * Thrown when the BO "Validation" textarea references a constraint name that is not part of the
 * ExtraPropertyConstraintMapper whitelist.
 *
 * Surfaces typos and unsupported constraints to the user instead of silently dropping them.
 */
class UnknownExtraPropertyConstraintException extends ExtraPropertyException
{
}
