<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;
use RuntimeException;

class UpdateCombinationImagesFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @When I associate :imageReferences to combination :combinationReference
     *
     * @param array $imageReferences
     * @param string $combinationReference
     */
    public function associateCombinationImages(array $imageReferences, string $combinationReference): void
    {
        $imageIds = array_map(function (string $imageReference) {
            return (int) $this->getSharedStorage()->get($imageReference);
        }, $imageReferences);

        $this->getCommandBus()->handle(new SetCombinationImagesCommand(
            (int) $this->getSharedStorage()->get($combinationReference),
            $imageIds
        ));
    }

    /**
     * @When I remove all images associated to combination :combinationReference
     *
     * @param string $combinationReference
     */
    public function removeCombinationImages(string $combinationReference): void
    {
        $this->getCommandBus()->handle(new RemoveAllCombinationImagesCommand(
            (int) $this->getSharedStorage()->get($combinationReference)
        ));
    }

    /**
     * @Then combination :combinationReference should have following images :imageReferences
     *
     * @param string $combinationReference
     * @param string[] $imageReferences
     */
    public function assertCombinationImages(string $combinationReference, array $imageReferences): void
    {
        $images = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getImageIds();
        Assert::assertEquals(count($images), count($imageReferences));
        foreach ($imageReferences as $imageReference) {
            $imageId = $this->getSharedStorage()->get($imageReference);
            if (!in_array($imageId, $images)) {
                throw new RuntimeException(sprintf(
                    'Could not find image %s for combination %s',
                    $imageId,
                    $combinationReference
                ));
            }
        }
    }

    /**
     * @Then combination :combinationReference should have the following cover :coverUrl
     *
     * @param string $combinationReference
     * @param string $coverUrl
     */
    public function assertCombinationCover(string $combinationReference, string $coverUrl): void
    {
        $combinationCoverUrl = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getCoverThumbnailUrl();
        $realImageUrl = $this->getRealImageUrl($coverUrl);
        Assert::assertEquals(
            $realImageUrl,
            $combinationCoverUrl,
            'Unexpected combination cover image url'
        );
    }

    /**
     * @Then combination :combinationReference should have no images
     *
     * @param string $combinationReference
     */
    public function assertNoImages(string $combinationReference): void
    {
        $images = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getImageIds();
        Assert::assertEmpty($images);
    }
}
