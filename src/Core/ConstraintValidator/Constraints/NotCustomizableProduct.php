<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\NotCustomizableProductValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for making sure that the selected product isn't customizable.
 */
class NotCustomizableProduct extends Constraint
{
    public $message = 'Customizable product cannot be selected.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return NotCustomizableProductValidator::class;
    }
}
