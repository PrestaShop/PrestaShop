<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception;

/**
 * Thrown when an extra property definition input fails a structural constraint.
 */
class ExtraPropertyConstraintException extends ExtraPropertyException
{
    /**
     * Thrown when the provided definition id is not a positive integer.
     */
    public const INVALID_ID = 1;

    /**
     * Thrown when the entity name contains invalid characters.
     */
    public const INVALID_ENTITY_NAME = 2;

    /**
     * Thrown when the property name contains invalid characters.
     */
    public const INVALID_PROPERTY_NAME = 3;
}
