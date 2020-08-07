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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateCategoriesFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I assign product :productReference to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assignToCategoriesIncludingNonExistingOnes(string $productReference, TableNode $table)
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
            $categoryIds
        );
    }

    /**
     * @When I delete all categories from product :productReference except the default one
     *
     * @param string $productReference
     */
    public function deleteAllProductCategoriesExceptDefault(string $productReference)
    {
        $this->assignProductToCategories($this->getSharedStorage()->get($productReference));
    }

    /**
     * @When I assign product :productReference to default category :categoryReference
     *
     * @param string $productReference
     * @param string $categoryReference
     */
    public function updateOnlyDefaultCategory(string $productReference, string $categoryReference)
    {
        $this->assignProductToCategories(
            $this->getSharedStorage()->get($productReference),
            $this->getSharedStorage()->get($categoryReference)
        );
    }

    /**
     * @Then product :productReference should be assigned to following categories:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertProductCategories(string $productReference, TableNode $table)
    {
        $data = $table->getRowsHash();
        $productForEditing = $actualCategoryIds = $this->getProductForEditing($productReference);
        $actualCategoryIds = $productForEditing->getCategoriesInformation()->getCategoryIds();
        sort($actualCategoryIds);

        $expectedCategoriesRef = PrimitiveUtils::castStringArrayIntoArray($data['categories']);
        $expectedCategoryIds = array_map(function (string $categoryReference) {
            return $this->getSharedStorage()->get($categoryReference);
        }, $expectedCategoriesRef);
        sort($expectedCategoryIds);

        $expectedDefaultCategoryId = $this->getSharedStorage()->get($data['default category']);
        $actualDefaultCategoryId = $productForEditing->getCategoriesInformation()->getDefaultCategoryId();

        Assert::assertEquals($expectedDefaultCategoryId, $actualDefaultCategoryId, 'Unexpected default category assigned to product');
        Assert::assertEquals($actualCategoryIds, $expectedCategoryIds, 'Unexpected categories assigned to product');
    }

    /**
     * @Then product :productReference should be assigned to default category
     *
     * @param string $productReference
     */
    public function assertProductAssignedToDefaultCategory(string $productReference)
    {
        $context = $this->getContainer()->get('prestashop.adapter.legacy.context')->getContext();
        $defaultCategoryId = (int) $context->shop->id_category;

        $productForEditing = $this->getProductForEditing($productReference);
        $productCategoriesInfo = $productForEditing->getCategoriesInformation();

        $belongsToDefaultCategory = false;
        foreach ($productCategoriesInfo->getCategoryIds() as $categoryId) {
            if ($categoryId === $defaultCategoryId) {
                $belongsToDefaultCategory = true;

                break;
            }
        }

        if ($productCategoriesInfo->getDefaultCategoryId() !== $defaultCategoryId || !$belongsToDefaultCategory) {
            throw new RuntimeException('Product is not assigned to default category');
        }
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

    /**
     * @param int $productId
     * @param int $defaultCategoryId
     * @param array $categoryIds
     */
    private function assignProductToCategories(int $productId, ?int $defaultCategoryId = null, array $categoryIds = []): void
    {
        try {
            if (empty($categoryIds) && !empty($defaultCategoryId)) {
                $command = UpdateProductCategoriesCommand::updateOnlyDefault($productId, $defaultCategoryId);
            } elseif (empty($categoryIds) && empty($defaultCategoryId)) {
                $command = UpdateProductCategoriesCommand::deleteAllExceptDefault($productId);
            } else {
                $command = UpdateProductCategoriesCommand::replace($productId, $defaultCategoryId, $categoryIds);
            }

            $this->getCommandBus()->handle($command);
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }
}
