<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\ExistingCustomerEmailValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for checking if customer with given email exists in current shop context
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ExistingCustomerEmail extends Constraint
{
    public $message = 'This email address is not registered.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return ExistingCustomerEmailValidator::class;
    }
}
