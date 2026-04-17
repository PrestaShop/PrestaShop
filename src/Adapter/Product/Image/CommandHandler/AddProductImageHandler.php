<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Image\ProductImageFileValidator;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Uploader\ProductImageUploader;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\AddProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

/**
 * Handles @see AddProductImageCommand
 */
#[AsCommandHandler]
final class AddProductImageHandler implements AddProductImageHandlerInterface
{
    /**
     * @var ProductImageUploader
     */
    private $productImageUploader;

    /**
     * @var ProductImageFileValidator
     */
    private $imageValidator;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    public function __construct(
        ProductImageUploader $productImageUploader,
        ProductImageRepository $productImageRepository,
        ProductImageFileValidator $imageValidator
    ) {
        $this->productImageUploader = $productImageUploader;
        $this->imageValidator = $imageValidator;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductImageCommand $command): ImageId
    {
        $filePath = $command->getFilePath();
        $this->imageValidator->assertFileUploadLimits($filePath);
        $this->imageValidator->assertIsValidImageType($filePath);

        $image = $this->productImageRepository->create($command->getProductId(), $command->getShopConstraint());
        $this->productImageUploader->upload($image, $filePath);

        return new ImageId((int) $image->id);
    }
}
