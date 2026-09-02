<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\AddressZipCodeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Address zip code validation constraint
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AddressZipCode extends Constraint
{
    public $requiredMessage = 'A Zip/Postal code is required.';

    public $id_country;

    public $required;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return AddressZipCodeValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['id_country', 'required'];
    }
}
