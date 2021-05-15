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
        $images = $this->getCombinationForEditing($combinationReference)->getImageIds();
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
     * @Then combination :combinationReference should have no images
     *
     * @param string $combinationReference
     */
    public function assertNoImages(string $combinationReference): void
    {
        $images = $this->getCombinationForEditing($combinationReference)->getImageIds();
        Assert::assertEmpty($images);
    }
}
