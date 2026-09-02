<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CustomerNameValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class CustomerName is responsible of validating customer name according to several patterns.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class CustomerName extends Constraint
{
    public $message = 'The %s field is invalid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return CustomerNameValidator::class;
    }
}
