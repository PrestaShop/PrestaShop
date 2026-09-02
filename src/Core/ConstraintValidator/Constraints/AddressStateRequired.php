<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\AddressStateRequiredValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for address state choice by selected country value
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AddressStateRequired extends Constraint
{
    public $message = 'An address located in a country containing states must have a state selected.';

    public $id_country;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return AddressStateRequiredValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['id_country'];
    }
}
