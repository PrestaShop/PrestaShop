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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\File\Uploader\VirtualProductFileUploader;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductDownloadRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\CommandHandler\AddVirtualProductFileHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use ProductDownload;

/**
 * Handles @see AddVirtualProductFileCommand using legacy object model
 */
final class AddVirtualProductFileHandler implements AddVirtualProductFileHandlerInterface
{
    /**
     * @var VirtualProductFileUploader
     */
    private $virtualProductFileUploader;

    /**
     * @var ProductDownloadRepository
     */
    private $productDownloadRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param VirtualProductFileUploader $virtualProductFileUploader
     * @param ProductDownloadRepository $productDownloadRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        VirtualProductFileUploader $virtualProductFileUploader,
        ProductDownloadRepository $productDownloadRepository,
        ProductRepository $productRepository
    ) {
        $this->virtualProductFileUploader = $virtualProductFileUploader;
        $this->productDownloadRepository = $productDownloadRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddVirtualProductFileCommand $command): void
    {
        $this->productRepository->assertProductExists($command->getProductId());
        $uploadedFilePath = $this->virtualProductFileUploader->upload($command->getFilePath());
        $productDownload = $this->buildObjectModel($command, pathinfo($uploadedFilePath, PATHINFO_FILENAME));

        $this->productDownloadRepository->add($productDownload);
    }

    /**
     * @param AddVirtualProductFileCommand $command
     * @param string $uploadedFileName
     *
     * @return ProductDownload
     */
    private function buildObjectModel(AddVirtualProductFileCommand $command, string $uploadedFileName): ProductDownload
    {
        $productDownload = new ProductDownload();
        $productDownload->id_product = $command->getProductId()->getValue();
        $productDownload->display_filename = $command->getDisplayName();
        $productDownload->filename = $uploadedFileName;
        $productDownload->nb_days_accessible = $command->getAccessDays() ?: 0;
        $productDownload->nb_downloadable = $command->getDownloadTimesLimit() ?: 0;
        $productDownload->date_expiration = $command->getExpirationDate() ?
            $command->getExpirationDate()->format(DateTime::DEFAULT_FORMAT) :
            null
        ;

        return $productDownload;
    }
}
