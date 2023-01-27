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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateCategoriesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I assign product :productReference to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assignToCategoriesForDefaultShop(string $productReference, TableNode $table)
    {
        $this->assignToCategories($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I assign product :productReference to following categories for shop :shopReference:
     *
     * @param string $productReference
     * @param TableNode $table
     * @param string $shopReference
     */
    public function assignToCategoriesForSpecificShop(string $productReference, TableNode $table, string $shopReference)
    {
        $this->assignToCategories($productReference, $table, ShopConstraint::shop($this->referenceToId($shopReference)));
    }

    /**
     * @When I assign product :productReference to following categories for all shops:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assignToCategoriesForAllShops(string $productReference, TableNode $table)
    {
        $this->assignToCategories($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @Then product :productReference should be assigned to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductCategoriesForDefaultShop(string $productReference, TableNode $table)
    {
        $this->assertProductCategories($productReference, $table, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference should be assigned to following categories for shop(s) :shopReferences:
     *
     * @param string $productReference
     * @param TableNode $table
     * @param string $shopReferences
     */
    public function assertProductCategoriesForShops(string $productReference, TableNode $table, string $shopReferences)
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->assertProductCategories($productReference, $table, $shopId);
        }
    }

    /**
     * @When I delete all categories from product :productReference
     *
     * @param string $productReference
     */
    public function deleteAllProductCategoriesForDefaultShop(string $productReference)
    {
        $this->deleteAllProductCategories($productReference, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I delete all categories from product :productReference for shop :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function deleteAllProductCategoriesForShop(string $productReference, string $shopReference)
    {
        $this->deleteAllProductCategories($productReference, ShopConstraint::shop($this->referenceToId($shopReference)));
    }

    /**
     * @When I delete all categories from product :productReference for all shops
     *
     * @param string $productReference
     */
    public function deleteAllProductCategoriesForAllShops(string $productReference)
    {
        $this->deleteAllProductCategories($productReference, ShopConstraint::allShops());
    }

    /**
     * @Then I should get error that assigning product to categories failed
     */
    public function assertFailedUpdateCategoriesError()
    {
        $this->assertLastErrorIs(
            CannotUpdateProductException::class,
            CannotUpdateProductException::FAILED_UPDATE_CATEGORIES
        );
    }

    private function deleteAllProductCategories(string $productReference, ShopConstraint $shopConstraint)
    {
        try {
            $this->getCommandBus()->handle(new RemoveAllAssociatedProductCategoriesCommand(
                $this->getSharedStorage()->get($productReference),
                $shopConstraint
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    private function assignToCategories(string $productReference, TableNode $table, ShopConstraint $shopConstraint)
    {
        $data = $table->getRowsHash();
        $categoryReferences = PrimitiveUtils::castStringArrayIntoArray($data['categories']);

        // this random number is used on purpose to mimic non existing category id
        $nonExistingCategoryId = 50000;
        $categoryIds = [];
        foreach ($categoryReferences as $categoryReference) {
            if ($this->getSharedStorage()->exists($categoryReference)) {
                $categoryIds[] = $this->getSharedStorage()->get($categoryReference);
            } else {
                $categoryIds[] = $nonExistingCategoryId;
                ++$nonExistingCategoryId;
            }
        }

        if ($this->getSharedStorage()->exists($data['default category'])) {
            $defaultCategoryId = $this->getSharedStorage()->get($data['default category']);
        } else {
            $defaultCategoryId = $nonExistingCategoryId;
        }

        $this->assignProductToCategories(
            $this->getSharedStorage()->get($productReference),
            $defaultCategoryId,
            $categoryIds,
            $shopConstraint
        );
    }

    private function assertProductCategories(string $productReference, TableNode $table, int $shopId)
    {
        $productForEditing = $this->getProductForEditing($productReference, $shopId);
        $expectedCategories = $table->getColumnsHash();
        $categoriesInfo = $productForEditing->getCategoriesInformation();
        $actualCategories = $categoriesInfo->getCategoriesInformation();

        Assert::assertCount(
            count($expectedCategories),
            $actualCategories,
            sprintf('Expected and actual categories count doesn\'t match for shop %d', $shopId)
        );

        $expectedDefaultCategoryId = null;
        foreach ($actualCategories as $categoryInformation) {
            $actualId = $categoryInformation->getId();
            // We cannot anticipate categories ordering (and we don't really care) so we find related expected category by id
            $relativeExpectedCategories = array_filter(
                $expectedCategories,
                function (array $expectedCategory) use ($actualId) {
                    return $actualId === $this->getSharedStorage()->get($expectedCategory['id reference']);
                });
            Assert::assertNotEmpty($relativeExpectedCategories, sprintf(
                'Did not expect to find category %s in the list for shop %d',
                $categoryInformation->getName(),
                $shopId
            ));
            // Only one category should be provided in feature, but array filter returns array of found items, so we get first
            $expectedCategory = reset($relativeExpectedCategories);

            Assert::assertEquals(
                $categoryInformation->getId(),
                $this->getSharedStorage()->get($expectedCategory['id reference']),
                'Unexpected category id'
            );
            Assert::assertEquals(
                $expectedCategory['name'],
                $categoryInformation->getName(),
                'Category localized names doesn\'t match'
            );

            if (PrimitiveUtils::castStringBooleanIntoBoolean($expectedCategory['is default'])) {
                $expectedDefaultCategoryId = $categoryInformation->getId();
            }
        }

        Assert::assertEquals(
            $expectedDefaultCategoryId,
            $categoriesInfo->getDefaultCategoryId(),
            'Unexpected default category id'
        );
    }

    /**
     * @param int $productId
     * @param int $defaultCategoryId
     * @param array $categoryIds
     */
    private function assignProductToCategories(int $productId, int $defaultCategoryId, array $categoryIds, ShopConstraint $shopConstraint): void
    {
        try {
            $this->getCommandBus()->handle(new SetAssociatedProductCategoriesCommand(
                $productId,
                $defaultCategoryId,
                $categoryIds,
                $shopConstraint
            ));
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
