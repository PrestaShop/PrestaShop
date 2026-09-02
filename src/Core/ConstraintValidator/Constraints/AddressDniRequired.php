<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\AddressDniRequiredValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for address DNI according to the country value and fields requirement
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AddressDniRequired extends Constraint
{
    public $message = 'This field cannot be empty.';

    public $id_country;

    public $required;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return AddressDniRequiredValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['id_country', 'required'];
    }
}
