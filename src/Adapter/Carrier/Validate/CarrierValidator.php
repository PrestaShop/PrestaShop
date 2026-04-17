<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\Validate;

use Carrier;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedLogoImageExtensionException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageFileNotFoundException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageSizeException;

/**
 * Validates carrier properties using legacy object model
 */
class CarrierValidator extends AbstractObjectModelValidator
{
    protected const AVAILABLE_IMAGE_MIMETYPE = ['image/jpeg'];

    protected const MAX_IMAGE_SIZE_IN_BYTES = 8 * 1000000;

    public function __construct(
        private readonly GroupRepository $groupRepository,
    ) {
    }

    /**
     * @param Carrier $carrier
     *
     * @throws CoreException
     */
    public function validate(Carrier $carrier): void
    {
        $this->validateGeneral($carrier);
        $this->validateShipping($carrier);
    }

    public function validateLogoUpload(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new ImageFileNotFoundException('The uploaded image does not exist.');
        }

        if (filesize($filePath) > self::MAX_IMAGE_SIZE_IN_BYTES) {
            throw UploadedImageSizeException::build(self::MAX_IMAGE_SIZE_IN_BYTES);
        }

        $extension = mime_content_type($filePath);
        if (!in_array($extension, self::AVAILABLE_IMAGE_MIMETYPE, true)) {
            throw new NotSupportedLogoImageExtensionException(sprintf(
                'Not supported "%s" image logo mime type. Supported mime types are "%s"',
                $extension,
                implode(',', self::AVAILABLE_IMAGE_MIMETYPE
                )));
        }

        if (!ImageManager::checkImageMemoryLimit($filePath)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }
    }

    public function validateGroupsExist(array $groupIds): void
    {
        foreach ($groupIds as $groupId) {
            $this->groupRepository->assertGroupExists(new GroupId((int) $groupId));
        }
    }

    /**
     * @throws CoreException
     */
    private function validateGeneral(Carrier $carrier): void
    {
        $this->validateObjectModelProperty($carrier, 'name', CarrierConstraintException::class, CarrierConstraintException::INVALID_NAME);
        $this->validateObjectModelProperty($carrier, 'grade', CarrierConstraintException::class, CarrierConstraintException::INVALID_GRADE);
        $this->validateObjectModelProperty($carrier, 'url', CarrierConstraintException::class, CarrierConstraintException::INVALID_TRACKING_URL);
        $this->validateObjectModelProperty($carrier, 'position', CarrierConstraintException::class, CarrierConstraintException::INVALID_POSITION);
        $this->validateObjectModelLocalizedProperty($carrier, 'delay', CarrierConstraintException::class, CarrierConstraintException::INVALID_DELAY);
        $this->validateObjectModelProperty($carrier, 'max_width', CarrierConstraintException::class, CarrierConstraintException::INVALID_MAX_WIDTH);
        $this->validateObjectModelProperty($carrier, 'max_height', CarrierConstraintException::class, CarrierConstraintException::INVALID_MAX_HEIGHT);
        $this->validateObjectModelProperty($carrier, 'max_depth', CarrierConstraintException::class, CarrierConstraintException::INVALID_MAX_DEPTH);
        $this->validateObjectModelProperty($carrier, 'max_weight', CarrierConstraintException::class, CarrierConstraintException::INVALID_MAX_WEIGHT);
    }

    /**
     * @throws CoreException
     */
    private function validateShipping(Carrier $carrier): void
    {
        $this->validateObjectModelProperty($carrier, 'shipping_handling', CarrierConstraintException::class, CarrierConstraintException::INVALID_SHIPPING_HANDLING);
        $this->validateObjectModelProperty($carrier, 'is_free', CarrierConstraintException::class, CarrierConstraintException::INVALID_IS_FREE);
        $this->validateObjectModelProperty($carrier, 'shipping_method', CarrierConstraintException::class, CarrierConstraintException::INVALID_SHIPPING_METHOD);
        $this->validateObjectModelProperty($carrier, 'range_behavior', CarrierConstraintException::class, CarrierConstraintException::INVALID_RANGE_BEHAVIOR);

        // A Carrier cannot be both shipping handling and free
        if ($carrier->shipping_handling && $carrier->is_free) {
            throw new CarrierConstraintException(
                'Carrier cannot be both shipping handling and free',
                CarrierConstraintException::INVALID_HAS_ADDITIONAL_HANDLING_FEE_WITH_FREE_SHIPPING
            );
        }
    }
}
