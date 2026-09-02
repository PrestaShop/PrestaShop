<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\NoTagsValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for making sure there are no HTML or PHP tags to be removed by strip_tags
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NoTags extends Constraint
{
    public $message = 'Field must not contain HTML or PHP tags';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return NoTagsValidator::class;
    }
}
