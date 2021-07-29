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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\DeleteProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\DummyFileUploader;

class ProductImageFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    public function __construct()
    {
        $this->productImageRepository = $this->getContainer()->get('prestashop.adapter.product.image.repository.product_image_repository');
    }

    /**
     * @Given following image types should be applicable to products:
     */
    public function assertProductsImageTypesExists(TableNode $tableNode)
    {
        $dataRows = $tableNode->getColumnsHash();
        $imageTypes = $this->productImageRepository->getProductImageTypes();

        Assert::assertEquals(
            count($dataRows),
            count($imageTypes),
            'Expected and actual image types count does not match'
        );

        foreach ($dataRows as $key => $expectedType) {
            $actualType = $imageTypes[$key];
            Assert::assertEquals($expectedType['name'], $actualType->name, 'Unexpected image type name');
            Assert::assertEquals($expectedType['width'], $actualType->width, 'Unexpected image type width');
            Assert::assertEquals($expectedType['height'], $actualType->width, 'Unexpected image type height');

            $this->getSharedStorage()->set($expectedType['reference'], (int) $actualType->id);
        }
    }

    /**
     * @When I add new image :imageReference named :fileName to product :productReference
     *
     * @param string $imageReference
     * @param string $fileName
     * @param string $productReference
     */
    public function uploadImage(string $imageReference, string $fileName, string $productReference): void
    {
        $pathName = DummyFileUploader::upload($fileName);

        $imageId = $this->getCommandBus()->handle(new AddProductImageCommand(
            $this->getSharedStorage()->get($productReference),
            $pathName
        ));

        $this->getSharedStorage()->set($imageReference, $imageId->getValue());

        // Save uploaded file MD5 for future checks
        if ($this->getSharedStorage()->exists($fileName)) {
            return;
        }

        /** @var ProductImage $productImage */
        $productImage = $this->getQueryBus()->handle(new GetProductImage($imageId->getValue()));

        $imagePath = $this->getImagePath($productImage->getImageId());
        $this->getSharedStorage()->set($fileName, md5_file($imagePath));
    }

    /**
     * @When I update image :imageReference with following information:
     *
     * @param string $imageReference
     * @param TableNode $tableNode
     */
    public function updateImage(string $imageReference, TableNode $tableNode)
    {
        $dataRows = $this->localizeByRows($tableNode);
        $imageId = (int) $this->getSharedStorage()->get($imageReference);

        $command = new UpdateProductImageCommand($imageId);
        if (isset($dataRows['file'])) {
            $pathName = DummyFileUploader::upload($dataRows['file']);
            $command->setFilePath($pathName);
        }

        if (isset($dataRows['cover'])) {
            $command->setIsCover(PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['cover']));
        }

        if (isset($dataRows['legend'])) {
            $command->setLocalizedLegends($dataRows['legend']);
        }

        if (isset($dataRows['position'])) {
            $command->setPosition((int) $dataRows['position']);
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then image :imageReference should have same file as :fileName
     *
     * @param string $imageReference
     * @param string $fileName
     */
    public function assertImageFile(string $imageReference, string $fileName): void
    {
        $imageId = (int) $this->getSharedStorage()->get($imageReference);

        /** @var ProductImage $productImage */
        $productImage = $this->getQueryBus()->handle(new GetProductImage($imageId));

        // This was previously saved during image upload
        $generatedDummyMD5 = $this->getSharedStorage()->get($fileName);

        $imagePath = $this->getImagePath($productImage->getImageId());
        if ($generatedDummyMD5 !== md5_file($imagePath)) {
            throw new RuntimeException(sprintf(
                'Expected files dummy %s and image %s to be identical',
                $fileName,
                $imagePath
            ));
        }
    }

    /**
     * @Given images :imageReferences should have following types generated:
     *
     * @param string[] $imageReferences
     * @param TableNode $tableNode
     */
    public function assertProductImageTypesGenerated(array $imageReferences, TableNode $tableNode)
    {
        $dataRows = $tableNode->getColumnsHash();

        foreach ($imageReferences as $imageReference) {
            $imageId = $this->getSharedStorage()->get($imageReference);
            foreach ($dataRows as $dataRow) {
                $imgPath = $this->parseGeneratedImagePath($imageId, $dataRow['name']);
                if (!file_exists($imgPath)) {
                    throw new RuntimeException(sprintf('File "%s" does not exist', $imgPath));
                }

                $imgInfo = getimagesize($imgPath);
                Assert::assertEquals(
                    $dataRow['width'],
                    $imgInfo[0],
                    sprintf('Unexpected generated image "%s" width', $dataRow['name'])
                );
                Assert::assertEquals(
                    $dataRow['height'],
                    $imgInfo[1],
                    sprintf('Unexpected generated image "%s" height', $dataRow['name'])
                );
            }
        }
    }

    /**
     * @When I delete product image :imageReference
     *
     * @param string $imageReference
     */
    public function deleteProductImage(string $imageReference): void
    {
        $imageId = (int) $this->getSharedStorage()->get($imageReference);

        $this->getCommandBus()->handle(new DeleteProductImageCommand($imageId));
    }

    /**
     * @Then following types for image :imageReference should be removed:
     *
     * @param string $imageReference
     */
    public function assertProductImageTypesRemoved(string $imageReference, TableNode $tableNode): void
    {
        $dataRows = $tableNode->getColumnsHash();

        $imageId = $this->getSharedStorage()->get($imageReference);
        foreach ($dataRows as $dataRow) {
            $imgPath = $this->parseGeneratedImagePath($imageId, $dataRow['name']);
            if (file_exists($imgPath)) {
                throw new RuntimeException(sprintf('File "%s" should not exist', $imgPath));
            }
        }
        $imageFolder = $this->getImageGenerationPath($imageId);
        if (file_exists($imageFolder)) {
            throw new RuntimeException(sprintf('Folder "%s" should not exist', $imageFolder));
        }
    }

    /**
     * @param int $imageId
     *
     * @return string
     */
    private function getImageGenerationPath(int $imageId): string
    {
        $directories = str_split((string) $imageId);
        $path = implode('/', $directories);

        return _PS_PROD_IMG_DIR_ . $path;
    }

    /**
     * @param int $imageId
     * @param string $imageTypeName
     *
     * @return string
     */
    private function parseGeneratedImagePath(int $imageId, string $imageTypeName): string
    {
        return $this->getImageGenerationPath($imageId) . '/' . $imageId . '-' . $imageTypeName . '.jpg';
    }

    /**
     * @Then product :productReference should have no images
     *
     * @param string $productReference
     */
    public function assertProductHasNoImages(string $productReference): void
    {
        Assert::assertEmpty(
            $this->getProductImages($productReference),
            sprintf('No images expected for product "%s"', $productReference)
        );
    }

    /**
     * @Then product :productReference should have following cover :coverUrl
     *
     * @param string $productReference
     * @param string $coverUrl
     */
    public function assertProductCover(string $productReference, string $coverUrl): void
    {
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->getCommandBus()->handle(new GetProductForEditing(
            (int) $this->getSharedStorage()->get($productReference)
        ));
        $realImageUrl = $this->getRealImageUrl($coverUrl);

        Assert::assertEquals($realImageUrl, $productForEditing->getCoverThumbnailUrl());
    }

    /**
     * @Then product :productReference should have following images:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertProductImages(string $productReference, TableNode $tableNode): void
    {
        $images = $this->getProductImages($productReference);
        $dataRows = $this->localizeByColumns($tableNode);

        Assert::assertEquals(
            count($images),
            count($dataRows),
            'Expected and actual images count does not match'
        );

        $imagesById = [];
        foreach ($images as $image) {
            $imagesById[$image->getImageId()] = $image;
        }

        foreach ($dataRows as $dataRow) {
            $rowImageId = (int) $this->getSharedStorage()->get($dataRow['image reference']);
            if (!isset($imagesById[$rowImageId])) {
                throw new RuntimeException(sprintf('Cannot find image %s in product images.', $dataRow['image reference']));
            }
            $actualImage = $imagesById[$rowImageId];

            Assert::assertEquals(
                PrimitiveUtils::castStringBooleanIntoBoolean($dataRow['is cover']),
                $actualImage->isCover(),
                'Unexpected cover image'
            );
            Assert::assertEquals(
                $dataRow['legend'],
                $actualImage->getLocalizedLegends(),
                'Unexpected image legend'
            );
            Assert::assertEquals(
                PrimitiveUtils::castStringIntegerIntoInteger($dataRow['position']),
                $actualImage->getPosition(),
                'Unexpected image position'
            );

            $imagePath = $this->getImagePath($actualImage->getImageId());
            if (!file_exists($imagePath)) {
                throw new RuntimeException(sprintf('File "%s" does not exist', $imagePath));
            }

            if (empty($dataRow['image url'])) {
                Assert::assertNull($actualImage->getImageUrl(), 'Unexpected product image');
            } else {
                $realImageUrl = $this->getRealImageUrl($dataRow['image url']);
                Assert::assertEquals(
                    $realImageUrl,
                    $actualImage->getImageUrl(),
                    'Unexpected product image url'
                );
            }

            if (empty($dataRow['thumbnail url'])) {
                Assert::assertNull($actualImage->getThumbnailUrl(), 'Unexpected product thumbnail image');
            } else {
                $realImageUrl = $this->getRealImageUrl($dataRow['thumbnail url']);
                Assert::assertEquals(
                    $realImageUrl,
                    $actualImage->getThumbnailUrl(),
                    'Unexpected product thumbnail url'
                );
            }
        }
    }

    /**
     * @param int $imageId
     *
     * @return string
     */
    private function getImagePath(int $imageId): string
    {
        $imageFolder = implode('/', str_split((string) $imageId));

        return _PS_PROD_IMG_DIR_ . '/' . $imageFolder . '/' . $imageId . '.jpg';
    }

    /**
     * @param string $productReference
     *
     * @return ProductImage[]
     */
    private function getProductImages(string $productReference): array
    {
        return $this->getQueryBus()->handle(new GetProductImages(
            $this->getSharedStorage()->get($productReference)
        ));
    }
}
