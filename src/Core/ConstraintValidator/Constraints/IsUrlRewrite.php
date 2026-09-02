<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\IsUrlRewriteValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class IsUrlRewrite is responsible of validating url rewrites according to several patterns
 * which differ when ascending urls are enabled or not.
 *
 * @Annotation
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class IsUrlRewrite extends Constraint
{
    public $message = '%s is invalid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return IsUrlRewriteValidator::class;
    }
}
