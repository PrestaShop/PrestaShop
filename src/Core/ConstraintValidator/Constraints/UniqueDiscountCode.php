<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\UniqueDiscountCodeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for making sure that a discount code isn't already used by another discount.
 */
class UniqueDiscountCode extends Constraint
{
    public $message = 'The discount code is already used (conflict with discount "%s").';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return UniqueDiscountCodeValidator::class;
    }
}
