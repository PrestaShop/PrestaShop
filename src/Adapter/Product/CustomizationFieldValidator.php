<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

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
     * @param string $propertyName
     *
     * @throws CoreException
     */
    public function validateProperty(CustomizationField $customizationField, string $propertyName): void
    {
        parent::validateObjectModelProperty(
            $customizationField,
            $propertyName,
            CustomizationFieldConstraintException::class,
            $this->getErrorCode($propertyName)
        );
    }

    /**
     * @param CustomizationField $customizationField
     * @param string $propertyName
     *
     * @throws CoreException
     */
    public function validateLocalizedProperty(CustomizationField $customizationField, string $propertyName): void
    {
        $this->validateObjectModelLocalizedProperty(
            $customizationField,
            $propertyName,
            CustomizationFieldConstraintException::class,
            $this->getErrorCode($propertyName)
        );
    }

    /**
     * @param string $propertyName
     *
     * @return int
     */
    private function getErrorCode(string $propertyName): int
    {
        $codesByName = [
            'name' => CustomizationFieldConstraintException::INVALID_NAME,
            'type' => CustomizationFieldConstraintException::INVALID_TYPE,
            'id' => CustomizationFieldConstraintException::INVALID_ID,
        ];

        return $codesByName[$propertyName];
    }
}
