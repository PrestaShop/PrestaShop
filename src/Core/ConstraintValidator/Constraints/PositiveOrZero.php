<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\PositiveOrZeroValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating if value is not negative and is not zero
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class PositiveOrZero extends Constraint
{
    public $message = 'This value should be either positive or zero.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return PositiveOrZeroValidator::class;
    }
}
