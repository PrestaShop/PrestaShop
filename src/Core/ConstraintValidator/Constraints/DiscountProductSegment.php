<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\DiscountProductSegmentValidator;
use Symfony\Component\Validator\Constraint;

class DiscountProductSegment extends Constraint
{
    public function validatedBy()
    {
        return DiscountProductSegmentValidator::class;
    }
}
