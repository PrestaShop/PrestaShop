<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\Validate;

use FeatureValue;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates FeatureValue properties using legacy object model
 */
class FeatureValueValidator extends AbstractObjectModelValidator
{
    /**
     * @param FeatureValue $featureValue
     *
     * @throws CoreException
     * @throws FeatureValueConstraintException
     */
    public function validate(FeatureValue $featureValue): void
    {
        $this->validateFeatureValueProperty($featureValue, 'id_feature', FeatureValueConstraintException::INVALID_FEATURE_ID);
        $this->validateObjectModelLocalizedProperty($featureValue, 'value', FeatureValueConstraintException::class, FeatureValueConstraintException::INVALID_VALUE);
    }

    /**
     * @param FeatureValue $featureValue
     * @param string $property
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws FeatureValueConstraintException
     */
    private function validateFeatureValueProperty(FeatureValue $featureValue, string $property, int $errorCode): void
    {
        $this->validateObjectModelProperty($featureValue, $property, FeatureValueConstraintException::class, $errorCode);
    }
}
