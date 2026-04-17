<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update;

use PrestaShop\PrestaShop\Adapter\File\Uploader\VirtualProductFileUploader;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use ProductDownload as VirtualProductFile;

/**
 * Provides update methods specific to virtual product
 * Legacy object ProductDownload is referred as VirtualProductFile in Core
 */
class VirtualProductUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var VirtualProductFileUploader
     */
    private $virtualProductFileUploader;

    /**
     * @var VirtualProductFileRepository
     */
    private $virtualProductFileRepository;

    /**
     * @param ProductRepository $productRepository
     * @param VirtualProductFileUploader $virtualProductFileUploader
     * @param VirtualProductFileRepository $virtualProductFileRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        VirtualProductFileUploader $virtualProductFileUploader,
        VirtualProductFileRepository $virtualProductFileRepository
    ) {
        $this->productRepository = $productRepository;
        $this->virtualProductFileUploader = $virtualProductFileUploader;
        $this->virtualProductFileRepository = $virtualProductFileRepository;
    }

    /**
     * @param VirtualProductFile $virtualProductFile
     * @param string|null $newFilePath
     */
    public function updateFile(VirtualProductFile $virtualProductFile, ?string $newFilePath): void
    {
        if ($newFilePath) {
            $uploadedFilePath = $this->virtualProductFileUploader->replace($newFilePath, $virtualProductFile->filename);
            $virtualProductFile->filename = pathinfo($uploadedFilePath, PATHINFO_FILENAME);
        }

        $this->virtualProductFileRepository->update($virtualProductFile);
    }

    /**
     * Add virtual product file to a product
     * Legacy object ProductDownload is referred as VirtualProductFile in Core
     *
     * @param ProductId $productId
     * @param string $filePath
     * @param VirtualProductFile $virtualProductFile
     *
     * @return VirtualProductFileId
     *
     * @throws InvalidProductTypeException
     * @throws VirtualProductFileConstraintException
     */
    public function addFile(ProductId $productId, string $filePath, VirtualProductFile $virtualProductFile): VirtualProductFileId
    {
        $product = $this->productRepository->getProductByDefaultShop($productId);
        if ($product->product_type !== ProductType::TYPE_VIRTUAL) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_VIRTUAL_TYPE);
        }

        try {
            $this->virtualProductFileRepository->findByProductId($productId);
            throw new VirtualProductFileConstraintException(
                sprintf('File already exists for product #%d', $product->id),
                VirtualProductFileConstraintException::ALREADY_HAS_A_FILE
            );
        } catch (VirtualProductFileNotFoundException) {
            // Expected behaviour, the product should have no virtual file yet
        }

        $uploadedFilePath = $this->virtualProductFileUploader->upload($filePath);
        $virtualProductFile->filename = pathinfo($uploadedFilePath, PATHINFO_FILENAME);
        $virtualProductFile->id_product = $productId->getValue();

        return $this->virtualProductFileRepository->add($virtualProductFile);
    }

    /**
     * @param VirtualProductFileId $virtualProductFileId
     */
    public function deleteFile(VirtualProductFileId $virtualProductFileId): void
    {
        $virtualProductFile = $this->virtualProductFileRepository->get($virtualProductFileId);
        $this->virtualProductFileUploader->remove($virtualProductFile->filename);

        $this->virtualProductFileRepository->delete($virtualProductFileId);
    }

    /**
     * @param ProductId $productId
     *
     * @throws InvalidProductTypeException
     */
    public function deleteFileForProduct(ProductId $productId): void
    {
        $product = $this->productRepository->getProductByDefaultShop($productId);
        if ($product->product_type !== ProductType::TYPE_VIRTUAL) {
            throw new InvalidProductTypeException(InvalidProductTypeException::EXPECTED_VIRTUAL_TYPE);
        }

        try {
            $virtualProductFile = $this->virtualProductFileRepository->findByProductId($productId);
        } catch (VirtualProductFileNotFoundException) {
            // No virtual file found, nothing to remove
            return;
        }

        $this->virtualProductFileUploader->remove($virtualProductFile->filename);
        $this->virtualProductFileRepository->delete(new VirtualProductFileId((int) $virtualProductFile->id));
    }
}
