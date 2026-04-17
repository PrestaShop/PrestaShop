<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use Attribute;
use PrestaShop\PrestaShop\Core\ConstraintValidator\InstalledApiResourceScopeValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class InstalledApiResourceScope extends Constraint
{
    public $message = 'The scopes %scope_names% are not associated to any installed API.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return InstalledApiResourceScopeValidator::class;
    }
}
