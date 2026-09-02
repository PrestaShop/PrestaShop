<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\NotBlankWhenRequiredValidator;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NotBlankWhenRequired extends NotBlank
{
    public $required;

    public function validatedBy()
    {
        return NotBlankWhenRequiredValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['required'];
    }
}
