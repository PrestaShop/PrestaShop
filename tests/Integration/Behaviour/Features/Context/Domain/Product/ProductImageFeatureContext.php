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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductImage;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\DummyFileUploader;

class ProductImageFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I add new product :productReference image :imageReference named :fileName
     *
     * @param string $productReference
     * @param string $imageReference
     * @param string $fileName
     */
    public function uploadImage(string $productReference, string $imageReference, string $fileName): void
    {
        //@todo: behats database contains empty ImageType.
        $pathName = DummyFileUploader::upload($fileName);

        $imageId = $this->getCommandBus()->handle(new AddProductImageCommand(
            $this->getSharedStorage()->get($productReference),
            $pathName
        ));

        $this->getSharedStorage()->set($imageReference, $imageId->getValue());
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
        $dataRows = $tableNode->getColumnsHash();

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
                $this->parseLocalizedArray($dataRow['legend']),
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
