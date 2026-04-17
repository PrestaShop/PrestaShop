<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class CleanHtml is responsible for validating the html content to prevent from having javascript events
 * or script tags.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final class CleanHtml extends Constraint
{
    public $message = '%s is invalid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return CleanHtmlValidator::class;
    }
}
