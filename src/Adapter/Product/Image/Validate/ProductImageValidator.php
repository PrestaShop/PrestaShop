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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\Validate;

use Image;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ProductImageConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class ProductImageValidator extends AbstractObjectModelValidator
{
    public function validate(Image $image): void
    {
        $this->validateProductImageProperty($image, 'cover', ProductImageConstraintException::INVALID_COVER);
        $this->validateProductImageProperty($image, 'position', ProductImageConstraintException::INVALID_POSITION);
        $this->validateObjectModelLocalizedProperty(
            $image,
            'legend',
            ProductImageConstraintException::class,
            ProductImageConstraintException::INVALID_LEGENDS
        );
    }

    /**
     * @param Image $image
     * @param string $property
     * @param int $errorCode
     *
     * @throws CoreException
     * @throws ProductImageConstraintException
     */
    private function validateProductImageProperty(Image $image, string $property, int $errorCode): void
    {
        $this->validateObjectModelProperty($image, $property, ProductImageConstraintException::class, $errorCode);
    }
}
