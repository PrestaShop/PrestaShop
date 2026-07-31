<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

use Throwable;

/**
 * Thrown when registering or unregistering an extra property definition fails.
 *
 * The failure reason is carried by the exception code (see the constants), so callers
 * can react to a specific reason without a dedicated exception class per case. When the
 * failure comes from the database layer, the original driver exception is available as
 * the previous exception. When the failure aggregates several individual errors, they
 * are available through getErrors().
 */
class ExtraPropertyRegistryException extends ExtraPropertyException
{
    /**
     * The native base table the extra table must mirror does not exist — typically an
     * entity name that does not match any ObjectModel table, or a lang/shop scope on an
     * entity that has no *_lang / *_shop table.
     */
    public const BASE_TABLE_NOT_FOUND = 1;

    /**
     * The property is already registered on the entity under a different scope.
     */
    public const SCOPE_CONFLICT = 2;

    /**
     * The change would risk data stored in the live column (type or scope change,
     * STRING size decrease, nullable tightening, CHOICE enum value removal) and was
     * refused — such changes require unregister() + register().
     */
    public const DESTRUCTIVE_SCHEMA_CHANGE = 3;

    /**
     * Persisting or deleting the definition row failed.
     */
    public const PERSISTENCE_FAILURE = 4;

    /**
     * A DDL operation on the *_extra storage tables failed (table/column create, alter
     * or drop), or could not be attempted (base table has no primary key to mirror).
     */
    public const SCHEMA_FAILURE = 5;

    /**
     * The declared formType/formOptions cannot build a working form field.
     */
    public const INVALID_FORM_OPTIONS = 6;

    /**
     * @param list<string> $errors individual human-readable errors when the failure
     *                             aggregates several (only INVALID_FORM_OPTIONS provides
     *                             them so far — one entry per invalid form option)
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
        private readonly array $errors = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return list<string> empty when the failure carries no individual error detail
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
