<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\DiscountAmountValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating discount amount (adapted for the nested value structure).
 */
class DiscountAmount extends Constraint
{
    public string $invalidTypeMessage = 'Reduction type "%type%" is invalid. Allowed types are: %types%.';

    public string $invalidAmountValueMessage = 'Reduction value "%value%" is invalid. It must be greater than 0.';

    public string $invalidPercentageValueMessage = 'Reduction value "%value%" is invalid. Value must be more than zero and maximum %max%.';

    public function validatedBy(): string
    {
        return DiscountAmountValidator::class;
    }
}
