<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\DateRangeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Provides date range validation
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class DateRange extends Constraint
{
    public $message = 'The selected date range is not valid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return DateRangeValidator::class;
    }
}
