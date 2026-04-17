<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\Validate;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Validates CustomizationField field using legacy object model
 */
class CustomizationFieldValidator extends AbstractObjectModelValidator
{
    /**
     * @param CustomizationField $customizationField
     *
     * @throws CoreException
     */
    public function validate(CustomizationField $customizationField): void
    {
        $this::validateObjectModelProperty($customizationField, 'type', CustomizationFieldConstraintException::class, CustomizationFieldConstraintException::INVALID_TYPE);
        $this::validateObjectModelLocalizedProperty($customizationField, 'name', CustomizationFieldConstraintException::class, CustomizationFieldConstraintException::INVALID_NAME);
        $this::validateObjectModelProperty($customizationField, 'required', CustomizationFieldConstraintException::class);
        $this::validateObjectModelProperty($customizationField, 'is_module', CustomizationFieldConstraintException::class);
        $this::validateObjectModelProperty($customizationField, 'is_deleted', CustomizationFieldConstraintException::class);
        $this::validateObjectModelProperty($customizationField, 'id_product', CustomizationFieldConstraintException::class);
    }
}
