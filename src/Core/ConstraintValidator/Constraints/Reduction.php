<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ReductionValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating reduction
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class Reduction extends Constraint
{
    public $invalidTypeMessage = 'Reduction type "%type%" is invalid. Allowed types are: %types%.';

    public $invalidAmountValueMessage = 'Reduction value "%value%" is invalid. It must be greater than 0.';

    public $invalidPercentageValueMessage = 'Reduction value "%value%" is invalid. Value must be more than zero and maximum %max%.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return ReductionValidator::class;
    }
}
