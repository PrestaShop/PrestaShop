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
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Performs update of provided CustomizationField properties
 */
class CustomizationFieldUpdater extends AbstractObjectModelUpdater
{
    /**
     * @var CustomizationFieldValidator
     */
    private $customizationFieldValidator;

    /**
     * @param CustomizationFieldValidator $customizationFieldValidator
     */
    public function __construct(CustomizationFieldValidator $customizationFieldValidator)
    {
        $this->customizationFieldValidator = $customizationFieldValidator;
    }

    /**
     * @param CustomizationField $customizationField
     * @param array<string, mixed> $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function update(CustomizationField $customizationField, array $propertiesToUpdate, int $errorCode = 0)
    {
        $this->fillProperties($customizationField, $propertiesToUpdate);
        $this->customizationFieldValidator->validate($customizationField);
        $this->updateObjectModel($customizationField, CannotUpdateCustomizationFieldException::class, $errorCode);
    }

    /**
     * @param CustomizationField $customizationField
     * @param array $propertiesToUpdate
     */
    private function fillProperties(CustomizationField $customizationField, array $propertiesToUpdate): void
    {
        $this->fillLocalizedProperty($customizationField, 'name', $propertiesToUpdate);
        $this->fillProperty($customizationField, 'type', $propertiesToUpdate);
        $this->fillProperty($customizationField, 'required', $propertiesToUpdate);
        $this->fillProperty($customizationField, 'is_module', $propertiesToUpdate);
        $this->fillProperty($customizationField, 'id_product', $propertiesToUpdate);
        $this->fillProperty($customizationField, 'is_deleted', $propertiesToUpdate);
    }
}
