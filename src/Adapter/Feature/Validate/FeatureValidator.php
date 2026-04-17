<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\Validate;

use Feature;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;

class FeatureValidator extends AbstractObjectModelValidator
{
    public function validate(Feature $feature): void
    {
        $this->validateObjectModelProperty(
            $feature,
            'position',
            FeatureConstraintException::class,
            FeatureConstraintException::INVALID_POSITION
        );

        $this->validateObjectModelLocalizedProperty(
            $feature,
            'name',
            FeatureConstraintException::class,
            FeatureConstraintException::INVALID_NAME
        );
    }
}
