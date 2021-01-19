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

namespace PrestaShop\PrestaShop\Adapter\Product\Image\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Image\ImageValidator;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Uploader\ProductImageUploader;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\CommandHandler\AddProductImageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

/**
 * Handles @see AddProductImageCommand
 */
final class AddProductImageHandler implements AddProductImageHandlerInterface
{
    /**
     * @var ProductImageUploader
     */
    private $productImageUploader;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var ImageValidator
     */
    private $imageValidator;

    /**
     * @param ProductImageUploader $productImageUploader
     * @param ProductImageRepository $productImageRepository
     * @param ImageValidator $imageValidator
     * @param array $contextShopIds
     */
    public function __construct(
        ProductImageUploader $productImageUploader,
        ProductImageRepository $productImageRepository,
        ImageValidator $imageValidator,
        array $contextShopIds
    ) {
        $this->productImageUploader = $productImageUploader;
        $this->productImageRepository = $productImageRepository;
        $this->contextShopIds = $contextShopIds;
        $this->imageValidator = $imageValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductImageCommand $command): ImageId
    {
        $this->imageValidator->assertFileUploadLimits($command->getFilePath());
        $this->imageValidator->assertIsValidImageType($command->getFilePath());

        $image = $this->productImageRepository->create($command->getProductId(), $this->contextShopIds);
        $this->productImageUploader->upload($image, $command->getFilePath());

        return new ImageId((int) $image->id);
    }
}
