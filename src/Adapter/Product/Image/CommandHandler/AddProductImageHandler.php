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
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
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
     * @var ImageValidator
     */
    private $imageValidator;

    /**
     * @var ProductImageMultiShopRepository
     */
    private $productImageMultiShopRepository;

    /**
     * @param ProductImageUploader $productImageUploader
     * @param ProductImageMultiShopRepository $productImageMultiShopRepository
     * @param ImageValidator $imageValidator
     */
    public function __construct(
        ProductImageUploader $productImageUploader,
        ProductImageMultiShopRepository $productImageMultiShopRepository,
        ImageValidator $imageValidator
    ) {
        $this->productImageUploader = $productImageUploader;
        $this->imageValidator = $imageValidator;
        $this->productImageMultiShopRepository = $productImageMultiShopRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductImageCommand $command): ImageId
    {
        $this->imageValidator->assertFileUploadLimits($command->getFilePath());
        $this->imageValidator->assertIsValidImageType($command->getFilePath());

        $image = $this->productImageMultiShopRepository->create($command->getProductId(), $command->getShopConstraint());
        $this->productImageUploader->upload($image, $command->getFilePath());

        return new ImageId((int) $image->id);
    }
}
