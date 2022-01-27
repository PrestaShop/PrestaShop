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

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\Repository;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Validate\CustomizationFieldValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotAddCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for CustomizationField
 */
class CustomizationFieldRepository extends AbstractObjectModelRepository
{
    /**
     * @var CustomizationFieldValidator
     */
    private $customizationFieldValidator;

    /**
     * @param CustomizationFieldValidator $customizationFieldValidator
     */
    public function __construct(
        CustomizationFieldValidator $customizationFieldValidator
    ) {
        $this->customizationFieldValidator = $customizationFieldValidator;
    }

    /**
     * @param CustomizationFieldId $fieldId
     *
     * @return CustomizationField
     *
     * @throws CoreException
     */
    public function get(CustomizationFieldId $fieldId): CustomizationField
    {
        /** @var CustomizationField $customizationField */
        $customizationField = $this->getObjectModel(
            $fieldId->getValue(),
            CustomizationField::class,
            CustomizationFieldNotFoundException::class
        );

        return $customizationField;
    }

    /**
     * @param CustomizationField $customizationField
     * @param int $errorCode
     *
     * @return CustomizationFieldId
     *
     * @throws CoreException
     */
    public function add(CustomizationField $customizationField, int $errorCode = 0): CustomizationFieldId
    {
        $this->customizationFieldValidator->validate($customizationField);
        $this->addObjectModel($customizationField, CannotAddCustomizationFieldException::class, $errorCode);

        return new CustomizationFieldId((int) $customizationField->id);
    }

    /**
     * @param CustomizationField $customizationField
     *
     * @throws CannotUpdateCustomizationFieldException
     */
    public function update(CustomizationField $customizationField): void
    {
        $this->customizationFieldValidator->validate($customizationField);
        $this->updateObjectModel($customizationField, CannotUpdateCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function delete(CustomizationField $customizationField): void
    {
        $this->deleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function softDelete(CustomizationField $customizationField): void
    {
        $this->softDeleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }
}
