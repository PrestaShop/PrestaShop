<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ValidAddressFormatValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that a country address format string is parseable and references
 * only allowed object/field tokens, with all required fields present.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ValidAddressFormat extends Constraint
{
    public string $message = 'Invalid address format: %errors%';

    public function validatedBy(): string
    {
        return ValidAddressFormatValidator::class;
    }
}
