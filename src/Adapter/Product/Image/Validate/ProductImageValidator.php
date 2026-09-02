<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
