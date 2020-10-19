<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ErrorException;
use Hook;
use Image;
use PrestaShop\PrestaShop\Adapter\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Adapter\Product\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ProductImageUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;

final class ProductImageUploader extends AbstractImageUploader implements ProductImageUploaderInterface
{
    /**
     * @var UploadSizeConfigurationInterface
     */
    private $uploadSizeConfiguration;

    /**
     * @var ProductImagePathFactory
     */
    private $productImagePathFactory;

    /**
     * @var array
     */
    private $contextShopIdsList;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     * @param ProductImagePathFactory $productImagePathFactory
     * @param ProductImageRepository $productImageRepository
     * @param array $contextShopIdsList
     * @param int $contextShopId
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration,
        ProductImagePathFactory $productImagePathFactory,
        ProductImageRepository $productImageRepository,
        //@todo; how does context have shopIds & singe shopId ? Maybe we can clarify & harmonize it in MultistoreContextChecker?
        array $contextShopIdsList,
        int $contextShopId
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->productImageRepository = $productImageRepository;
        $this->contextShopIdsList = $contextShopIdsList;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(ImageId $imageId, string $filePath): void
    {
        $image = $this->productImageRepository->get($imageId);
        $this->productImagePathFactory->createDestinationDirectory($image);

        //@todo: this will unlink the image. Can we trust that the $filePath is in temp?
        $this->uploadFromTemp($filePath, $this->productImagePathFactory->getBasePath($image, true));
        $this->generateDifferentSizeImages($this->productImagePathFactory->getBasePath($image, false), 'products');

        Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $image->id_product]);
        //@Todo: double-check multishop
        $image->associateTo($this->contextShopIdsList);
        $this->deleteOldGeneratedImages($image);
    }

    /**
     * @param Image $image
     *
     * @throws CannotUnlinkImageException
     */
    private function deleteOldGeneratedImages(Image $image): void
    {
        $oldGeneratedImages = [
            _PS_TMP_IMG_DIR_ . 'product_' . (int) $image->id . '.jpg',
            _PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $image->id_product . '_' . $this->contextShopId . '.jpg',
        ];

        foreach ($oldGeneratedImages as $oldImage) {
            if (file_exists($oldImage)) {
                try {
                    unlink($oldImage);
                } catch (ErrorException $e) {
                    throw new CannotUnlinkImageException(
                        sprintf(
                            'Failed to remove old generated image "%s"',
                            $oldImage
                        ),
                        0,
                        $e
                    );
                }
            }
        }
    }
}
