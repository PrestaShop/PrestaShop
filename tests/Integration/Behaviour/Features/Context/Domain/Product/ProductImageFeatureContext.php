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
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\ProductImageSetting;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\SetProductImagesForAllShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\UpdateProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotRemoveCoverException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetShopProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopImageAssociation;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopImageAssociationCollection;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\Shop\ShopProductImagesCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\DummyFileUploader;

class ProductImageFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @var ProductImageRepository
     */
    private $productImageRepository;

    /**
     * @var ShopProductImagesCollection
     */
    private $shopProductImagesCollection;

    public function __construct()
    {
        $this->productImageRepository = $this->getContainer()->get('prestashop.adapter.product.image.repository.product_image_repository');
    }

    /**
     * @Given following image types should be applicable to products:
     */
    public function assertProductsImageTypesExists(TableNode $tableNode): void
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
            Assert::assertEquals($expectedType['height'], $actualType->height, 'Unexpected image type height');

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
    public function uploadImageForDefaultShop(string $imageReference, string $fileName, string $productReference): void
    {
        $this->uploadImageByShopConstraint($imageReference, $fileName, $productReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When /^I add new image "([^"]*)" named "([^"]*)" to product "([^"]*)" for shop "([^"]*)"$/
     *
     * @param string $imageReference
     * @param string $fileName
     * @param string $productReference
     * @param string $shopReference
     *
     * @return void
     *
     * @throws ShopException
     */
    public function uploadImageForSpecificShop(string $imageReference, string $fileName, string $productReference, string $shopReference): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->uploadImageByShopConstraint($imageReference, $fileName, $productReference, $shopConstraint);
    }

    /**
     * @When /^I add new image "([^"]*)" named "([^"]*)" to product "([^"]*)" for all shops$/
     *
     * @param string $imageReference
     * @param string $fileName
     * @param string $productReference
     *
     * @return void
     */
    public function uploadImageForAllShops(string $imageReference, string $fileName, string $productReference): void
    {
        $this->uploadImageByShopConstraint($imageReference, $fileName, $productReference, ShopConstraint::allShops());
    }

    /**
     * @When I update image :imageReference with following information:
     *
     * @param string $imageReference
     * @param TableNode $tableNode
     */
    public function updateImage(string $imageReference, TableNode $tableNode): void
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
    public function assertProductImageTypesGenerated(array $imageReferences, TableNode $tableNode): void
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
     * @Given /^the shop "([^"]*)" should have empty image details$/
     */
    public function theShopShouldNotHaveAnyImageDetails(string $shopReference)
    {
        $shopId = (int) $this->getSharedStorage()->get(trim($shopReference));
        Assert::assertTrue($this->shopProductImagesCollection
            ->filter(function (ShopProductImages $shopProductImages) use ($shopId): bool {
                return $shopProductImages->getShopId() === $shopId;
            })
            ->first()
            ->getProductImages()
            ->isEmpty()
        );
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

        return _PS_PRODUCT_IMG_DIR_ . $path;
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
        $productForEditing = $this->getProductForEditing($productReference);
        $realImageUrl = $this->getRealImageUrl($coverUrl);

        Assert::assertEquals($realImageUrl, $productForEditing->getCoverThumbnailUrl());
    }

    /**
     * @Then product :productReference should have following images:
     *
     * @param string $productReference
     * @param TableNode $tableNode
     */
    public function assertProductImagesForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertAllProductImages($productReference, $tableNode);
    }

    /**
     * @Given /^product "([^"]*)" should have following images for shop "([^"]*)":$/
     * @Given /^product "([^"]*)" should have following images for shops "([^"]*)":$/
     *
     * @param string $productReference
     * @param TableNode $table
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertProductImagesForAllShops(string $productReference, TableNode $table, string $shopReferences): void
    {
        $shopReferences = explode(',', $shopReferences);
        $shopIds = [];
        foreach ($shopReferences as $shopReference) {
            $shopIds[] = $this->getSharedStorage()->get(trim($shopReference));
        }

        foreach ($shopIds as $shopId) {
            $this->assertProductImagesByShopId($productReference, $table, $shopId);
        }
    }

    /**
     * @When /^I apply the following matrix of images for product "([^"]*)":$/
     */
    public function iApplyTheFollowingMatrixOfImagesForProduct(string $productReference, TableNode $table): void
    {
        $command = new SetProductImagesForAllShopCommand(
            $this->getSharedStorage()->get(trim($productReference))
        );
        foreach ($table as $data) {
            $command->addProductSetting(
                new ProductImageSetting(
                    $this->getSharedStorage()->get(trim($data['imageReference'])),
                    array_map(
                        function (string $shopReference): int {
                            return $this->getSharedStorage()->get(trim($shopReference));
                        },
                        explode(',', $data['shopReferences'])
                    )
                )
            );
        }
        try {
            $this->getCommandBus()->handle($command);
        } catch (CannotRemoveCoverException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I try to get every image details for product "([^"]*)" in every shop:$/
     */
    public function iTryToGetEveryImageDetailsForProductInEveryShop(string $productReference): void
    {
        $this->shopProductImagesCollection = $this->getQueryBus()->handle(new GetShopProductImages($this->getSharedStorage()->get($productReference)));
    }

    /**
     * @Transform table:image reference,cover,shopReference
     *
     * @param TableNode $tableNode
     *
     * @return ShopProductImagesCollection
     */
    public function transformShopProductImagesCollection(TableNode $tableNode): ShopProductImagesCollection
    {
        $dataRows = $tableNode->getColumnsHash();
        $productImagesByShop = [];
        foreach ($dataRows as $dataRow) {
            $shopId = (int) $this->getSharedStorage()->get(trim($dataRow['shopReference']));
            $productImagesByShop[$shopId][] = new ShopImageAssociation(
                (int) $this->getSharedStorage()->get(trim($dataRow['image reference'])),
                (int) $dataRow['cover'] === 1
            );
        }

        $shopProductImagesArray = array_map(
            function (int $shopId, array $productImages): ShopProductImages {
                return new ShopProductImages($shopId, ShopImageAssociationCollection::from(...$productImages));
            },
            array_keys($productImagesByShop),
            $productImagesByShop
        );

        return ShopProductImagesCollection::from(...$shopProductImagesArray);
    }

    /**
     * @Then /^I should have the followings image details:/
     */
    public function iShouldHaveTheFollowingsImageDetailsForShop(ShopProductImagesCollection $expectedShopProductImagesCollection)
    {
        foreach ($expectedShopProductImagesCollection as $expectedShopProductImage) {
            $actualShopProductImages = $this->shopProductImagesCollection
                ->filter(function (ShopProductImages $shopProductImages) use ($expectedShopProductImage): bool {
                    return $shopProductImages->getShopId() === $expectedShopProductImage->getShopId();
                })
                ->first();

            Assert::assertEquals(
                $expectedShopProductImage->getProductImages()->count(),
                $actualShopProductImages->getProductImages()->count()
            );

            foreach ($expectedShopProductImage->getProductImages() as $expectedProductImage) {
                Assert::assertContainsEquals(
                    new ShopImageAssociation($expectedProductImage->getImageId(), $expectedProductImage->isCover()),
                    $actualShopProductImages->getProductImages()
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

        return _PS_PRODUCT_IMG_DIR_ . '/' . $imageFolder . '/' . $imageId . '.jpg';
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

    /**
     * @param string $imageReference
     * @param string $fileName
     * @param string $productReference
     * @param ShopConstraint $shopConstraint
     *
     * @return void
     */
    private function uploadImageByShopConstraint(string $imageReference, string $fileName, string $productReference, ShopConstraint $shopConstraint): void
    {
        $pathName = DummyFileUploader::upload($fileName);

        $imageId = $this->getCommandBus()->handle(new AddProductImageCommand(
            $this->getSharedStorage()->get($productReference),
            $pathName,
            $shopConstraint
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
     * @param string $productReference
     * @param TableNode $tableNode
     *
     * @return void
     */
    private function assertAllProductImages(string $productReference, TableNode $tableNode): void
    {
        $images = $this->getProductImages($productReference);
        $this->assertProductImages($tableNode, $images);
    }

    /**
     * @param string $productReference
     * @param TableNode $tableNode
     * @param int $shopId
     *
     * @return void
     */
    private function assertProductImagesByShopId(string $productReference, TableNode $tableNode, int $shopId): void
    {
        $images = array_filter(
            $this->getProductImages($productReference),
            static function (ProductImage $productImage) use ($shopId): bool {
                return in_array($shopId, $productImage->getShopIds(), true);
            }
        );
        $this->assertProductImages($tableNode, $images);
    }

    /**
     * @Then /^I should get an error that you cannot remove an image which is a cover$/
     */
    public function iShouldGetAnErrorThatYouCannotRemoveAnImageWhichIsACover(): void
    {
        $this->assertLastErrorIs(CannotRemoveCoverException::class);
    }

    /**
     * @param TableNode $tableNode
     * @param ProductImage[] $images
     *
     * @return void
     */
    private function assertProductImages(TableNode $tableNode, array $images): void
    {
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

            if (isset($dataRow['is cover'])) {
                Assert::assertEquals(
                    PrimitiveUtils::castStringBooleanIntoBoolean($dataRow['is cover']),
                    $actualImage->isCover(),
                    'Unexpected cover image'
                );
            }

            if (isset($dataRow['legend'])) {
                Assert::assertEquals(
                    $dataRow['legend'],
                    $actualImage->getLocalizedLegends(),
                    'Unexpected image legend'
                );
            }

            Assert::assertEquals(
                PrimitiveUtils::castStringIntegerIntoInteger($dataRow['position']),
                $actualImage->getPosition(),
                'Unexpected image position'
            );

            $imagePath = $this->getImagePath($actualImage->getImageId());
            if (!file_exists($imagePath)) {
                throw new RuntimeException(sprintf('File "%s" does not exist', $imagePath));
            }

            if (isset($dataRow['image url'])) {
                $realImageUrl = $this->getRealImageUrl($dataRow['image url']);
                Assert::assertEquals(
                    $realImageUrl,
                    $actualImage->getImageUrl(),
                    'Unexpected product image url'
                );
            }

            if (isset($dataRow['thumbnail url'])) {
                $realImageUrl = $this->getRealImageUrl($dataRow['thumbnail url']);
                Assert::assertEquals(
                    $realImageUrl,
                    $actualImage->getThumbnailUrl(),
                    'Unexpected product thumbnail url'
                );
            }

            if (isset($dataRow['shops'])) {
                $expectedShopIds = [];
                $shops = explode(',', $dataRow['shops']);
                foreach ($shops as $shopReference) {
                    $expectedShopIds[] = $this->getSharedStorage()->get(trim($shopReference));
                }
                Assert::assertEquals(
                    $expectedShopIds,
                    $actualImage->getShopIds()
                );
            }
        }
    }
}
