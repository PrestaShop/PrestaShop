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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\Decimal\Exception\DivisionByZeroException;
use PrestaShop\PrestaShop\Adapter\File\Uploader\VirtualProductFileUploader;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductDownloadRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\File\Exception\CannotUnlinkFileException;
use PrestaShop\PrestaShop\Core\File\Exception\FileUploadException;
use ProductDownload;

/**
 * Provides update methods specific to virtual product
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
     * @var ProductDownloadRepository
     */
    private $productDownloadRepository;

    /**
     * @param ProductRepository $productRepository
     * @param VirtualProductFileUploader $virtualProductFileUploader
     * @param ProductDownloadRepository $productDownloadRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        VirtualProductFileUploader $virtualProductFileUploader,
        ProductDownloadRepository $productDownloadRepository
    ) {
        $this->productRepository = $productRepository;
        $this->virtualProductFileUploader = $virtualProductFileUploader;
        $this->productDownloadRepository = $productDownloadRepository;
    }

    /**
     * Add virtual product file to a product
     * Legacy ProductDownload is referred as VirtualProductFile in Core
     *
     * @param ProductId $productId
     * @param string $filePath
     * @param ProductDownload $productDownload
     *
     * @return VirtualProductFileId
     *
     * @throws VirtualProductFileConstraintException
     * @throws DivisionByZeroException
     * @throws VirtualProductFileNotFoundException
     * @throws CoreException
     * @throws CannotUnlinkFileException
     * @throws FileUploadException
     */
    public function addFile(ProductId $productId, string $filePath, ProductDownload $productDownload): VirtualProductFileId
    {
        $product = $this->productRepository->get($productId);

        if (!$product->is_virtual) {
            throw new VirtualProductFileConstraintException(
                'Only virtual product can have file',
                VirtualProductFileConstraintException::INVALID_PRODUCT_TYPE
            );
        }

        if ($this->productDownloadRepository->findByProductId($productId)) {
            throw new VirtualProductFileConstraintException(
                sprintf('File already exists for product #%d', $product->id),
                VirtualProductFileConstraintException::ALREADY_HAS_A_FILE
            );
        }

        $uploadedFilePath = $this->virtualProductFileUploader->upload($filePath);
        $productDownload->filename = pathinfo($uploadedFilePath, PATHINFO_FILENAME);
        $productDownload->id_product = $productId->getValue();

        return $this->productDownloadRepository->add($productDownload);
    }
}
