<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Exception;

/**
 * Thrown by the registry when a definition declares a formFieldType and/or formOptions with
 * which the back-office form field cannot be built (unknown form type class, unknown option,
 * invalid option value...). Raised BEFORE the definition is persisted, so no broken definition
 * ever reaches the registry — whatever the write path (BO form, CQRS command, module
 * registration).
 */
class InvalidExtraPropertyFormOptionsException extends ExtraPropertyException
{
    /**
     * @param list<string> $errors human-readable errors reported by FormOptionsValidator
     */
    public function __construct(
        private readonly array $errors,
    ) {
        parent::__construct(sprintf('Invalid extra property form options: %s', implode(' ', $errors)));
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
