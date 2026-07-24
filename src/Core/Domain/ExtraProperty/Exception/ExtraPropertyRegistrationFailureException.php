<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException as CoreExtraPropertyException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;

/**
 * Thrown when the registry fails to persist an extra property definition
 * (registration, update, or unregistration of the row and/or its SQL column).
 *
 * The failure reason is carried by the exception code (see the constants) so callers
 * (e.g. controller error-message maps) can display a precise message; the originating
 * core exception is available as the previous exception.
 */
class ExtraPropertyRegistrationFailureException extends ExtraPropertyException
{
    public const UNKNOWN = 0;

    /**
     * The entity's base table does not exist (unknown entity name, or missing
     * *_lang / *_shop table for the requested scope).
     */
    public const BASE_TABLE_MISSING = 1;

    /**
     * The property is already registered on the entity under a different scope.
     */
    public const SCOPE_CONFLICT = 2;

    /**
     * The change would risk data stored in the live column and was refused.
     */
    public const DESTRUCTIVE_CHANGE = 3;

    /**
     * Persisting or deleting the definition row failed.
     */
    public const PERSISTENCE_FAILURE = 4;

    /**
     * The table/column DDL failed.
     */
    public const SCHEMA_FAILURE = 5;

    /**
     * The declared formType/formOptions cannot build a working form field.
     */
    public const INVALID_FORM_OPTIONS = 6;

    /**
     * Builds the domain exception from the core exception thrown by the registry,
     * mapping the core reason code to the matching domain code and keeping the
     * core exception as the previous one.
     */
    public static function fromCoreException(CoreExtraPropertyException $coreException, string $message): self
    {
        $code = !$coreException instanceof ExtraPropertyRegistryException ? self::UNKNOWN : match ($coreException->getCode()) {
            ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND => self::BASE_TABLE_MISSING,
            ExtraPropertyRegistryException::SCOPE_CONFLICT => self::SCOPE_CONFLICT,
            ExtraPropertyRegistryException::DESTRUCTIVE_SCHEMA_CHANGE => self::DESTRUCTIVE_CHANGE,
            ExtraPropertyRegistryException::PERSISTENCE_FAILURE => self::PERSISTENCE_FAILURE,
            ExtraPropertyRegistryException::SCHEMA_FAILURE => self::SCHEMA_FAILURE,
            ExtraPropertyRegistryException::INVALID_FORM_OPTIONS => self::INVALID_FORM_OPTIONS,
            default => self::UNKNOWN,
        };

        return new self($message . ' ' . $coreException->getMessage(), $code, $coreException);
    }
}
