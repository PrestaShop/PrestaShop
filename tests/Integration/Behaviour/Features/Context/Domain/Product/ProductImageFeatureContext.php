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
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductImage;
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
        $this->productImageRepository = $this->getContainer()->get('prestashop.adapter.product.repository.product_image_repository');
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
     * @param int $imageId
     * @param string $imageTypeName
     *
     * @return string
     */
    private function parseGeneratedImagePath(int $imageId, string $imageTypeName): string
    {
        $directories = str_split((string) $imageId);
        $path = implode('/', $directories);

        return _PS_PROD_IMG_DIR_ . $path . '/' . $imageId . '-' . $imageTypeName . '.jpg';
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

        foreach ($dataRows as $key => $dataRow) {
            $actualImage = $images[$key];

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

            $path = _PS_PROD_IMG_DIR_ . $actualImage->getPath();

            if (!file_exists($path)) {
                throw new RuntimeException(sprintf('File "%s" does not exist', $path));
            }
        }
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
