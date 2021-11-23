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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Category;
use Configuration;
use Language;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CategoryTreeChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkUpdateCategoriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryMenuThumbnailImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\SetCategoryIsEnabledCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryIsEnabled;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\CategoryTreeIterator;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
    const EMPTY_VALUE = '';
    const DEFAULT_ROOT_CATEGORY_ID = 1;
    const JPG_IMAGE_TYPE = '.jpg';
    const THUMB0 = '0_thumb';
    const JPG_IMAGE_STRING = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
        . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
        . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
        . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

    const CATEGORY_POSITION_WAYS_MAP = [
        0 => 'Up',
        1 => 'Down',
    ];

    /** @var ContainerInterface */
    private $container;
    /** @var int */
    private $defaultLanguageId;
    /** @var string */
    private $psCatImgDir;

    /**
     * CategoryFeatureContext constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
        $this->defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');
        $this->psCatImgDir = _PS_CAT_IMG_DIR_;
    }

    /**
     * @Then I should see following root categories in ":langIso" language:
     *
     * @param TableNode $tableNode
     * @param string $langIso
     */
    public function assertRootCategoriesTree(TableNode $tableNode, string $langIso): void
    {
        $langId = Language::getIdByIso($langIso);
        $categoriesTree = $this->getQueryBus()->handle(new GetCategoriesTree($langId));

        Assert::assertNotEmpty($categoriesTree, 'Categories tree is empty');

        $this->assertCategoriesInTree($categoriesTree, $tableNode->getColumnsHash(), $langId);
    }

    /**
     * @Then I should see following categories in ":parentReference" category in ":langIso" language:
     *
     * @param TableNode $tableNode
     * @param string $langIso
     * @param string $parentReference
     */
    public function assertCategoriesTree(TableNode $tableNode, string $langIso, string $parentReference): void
    {
        $langId = Language::getIdByIso($langIso);
        $categoriesTree = $this->getQueryBus()->handle(new GetCategoriesTree($langId));

        Assert::assertNotEmpty($categoriesTree, 'Categories tree is empty');

        $parentCategoryId = $this->getSharedStorage()->get($parentReference);
        $actualCategories = $this->extractCategoriesByParent($categoriesTree, $parentCategoryId);

        $this->assertCategoriesInTree($actualCategories, $tableNode->getColumnsHash(), $langId);
    }

    /**
     * @When I add new category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function addNewCategoryWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
        $categoryTreeChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
        $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
        $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);

        /** @var CategoryId $categoryIdObject */
        $categoryIdObject = $this->getCommandBus()->handle(new AddCategoryCommand(
            [$this->defaultLanguageId => $testCaseData['Name']],
            [$this->defaultLanguageId => $testCaseData['Friendly URL']],
            PrimitiveUtils::castElementInType($testCaseData['Displayed'], PrimitiveUtils::TYPE_BOOLEAN),
            $parentCategoryId
        ));

        SharedStorage::getStorage()->set($categoryReference, $categoryIdObject->getValue());
    }

    /**
     * @Then category :categoryReference should have following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function categoryShouldHaveFollowingDetails(string $categoryReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $editableCategory = $this->getEditableCategory($categoryReference);
        $subCategories = $editableCategory->getSubCategories();

        /** @var EditableCategory $expectedEditableCategory */
        $expectedEditableCategory = $this->mapDataToEditableCategory(
            $testCaseData,
            $editableCategory->getId()->getValue(),
            $subCategories
        );

        Assert::assertEquals($expectedEditableCategory, $editableCategory);
    }

    /**
     * @When I edit category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function editCategoryWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $categoryId = SharedStorage::getStorage()->get($categoryReference);

        /** @var EditableCategory $expectedEditableCategory */
        $editableCategoryTestData = $this->mapDataToEditableCategory($testCaseData, $categoryId);

        /** @var EditCategoryCommand $command */
        $command = new EditCategoryCommand($categoryId);
        $command->setIsActive($editableCategoryTestData->isActive());
        $command->setLocalizedLinkRewrites($editableCategoryTestData->getLinkRewrite());
        $command->setLocalizedNames($editableCategoryTestData->getName());
        $command->setParentCategoryId($editableCategoryTestData->getParentId());
        $command->setLocalizedDescriptions($editableCategoryTestData->getDescription());
        $command->setLocalizedMetaTitles($editableCategoryTestData->getMetaTitle());
        $command->setLocalizedMetaDescriptions($editableCategoryTestData->getMetaDescription());
        $command->setLocalizedMetaKeywords($editableCategoryTestData->getMetaKeywords());
        $command->setAssociatedGroupIds($editableCategoryTestData->getGroupAssociationIds());

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete category :categoryReference choosing mode :deleteMode
     *
     * @param string $categoryReference
     * @param string $deleteMode
     */
    public function deleteCategory(string $categoryReference, string $deleteMode)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $this->getCommandBus()->handle(new DeleteCategoryCommand($categoryId, $deleteMode));
    }

    /**
     * @When I bulk delete categories :categoriesReferenceList choosing mode :deleteMode
     *
     * @param string $categoriesReferenceList
     * @param string $deleteMode
     */
    public function bulkDeleteCategories(string $categoriesReferenceList, string $deleteMode)
    {
        $categoryIds = [];
        $categoriesReferenceList = explode(',', $categoriesReferenceList);
        foreach ($categoriesReferenceList as $categoryReference) {
            $categoryIds[] = SharedStorage::getStorage()->get($categoryReference);
        }
        $this->getCommandBus()->handle(new BulkDeleteCategoriesCommand($categoryIds, $deleteMode));
    }

    /**
     * @Then category :categoryReference does not exist
     *
     * @param string $categoryReference
     */
    public function categoryDoesNotExist(string $categoryReference)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        try {
            $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));
        } catch (CategoryNotFoundException $e) {
            return;
        }
        throw new RuntimeException(sprintf('Category %s still exists', $categoryReference));
    }

    /**
     * @When I update category :categoryReference with generated position and following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function updateCategoryWithGeneratedPositionAndFollowingDetails(string $categoryReference, TableNode $table)
    {
        /** @var array $testCaseData */
        $testCaseData = $table->getRowsHash();

        $categoryId = SharedStorage::getStorage()->get($categoryReference);

        /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
        $categoryTreeChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.category_tree_choice_provider'
        );
        $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
        $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);

        $wayId = array_flip(self::CATEGORY_POSITION_WAYS_MAP)[$testCaseData['Way']];

        $this->getCommandBus()->handle(new UpdateCategoryPositionCommand(
            $categoryId,
            $parentCategoryId,
            $wayId,
            ['tr_' . $parentCategoryId . '_' . $categoryId], // generated position
            $testCaseData['Found first']
        ));
    }

    /**
     * @When I edit root category :rootCategory with following details:
     *
     * @param string $rootCategory
     * @param TableNode $table
     */
    public function editRootCategoryWithFollowingDetails(string $rootCategory, TableNode $table)
    {
        /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
        $categoryTreeChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
        $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
        $categoryId = $categoryTreeIterator->getCategoryId($rootCategory);

        SharedStorage::getStorage()->set($rootCategory, $categoryId);

        $testCaseData = $table->getRowsHash();
        $editableRootCategoryTestCaseData = $this->mapDataToEditableCategory($testCaseData, $categoryId);

        /** @var EditCategoryCommand $command */
        $command = new EditRootCategoryCommand($categoryId);
        $command->setIsActive($editableRootCategoryTestCaseData->isActive());
        $command->setLocalizedLinkRewrites($editableRootCategoryTestCaseData->getLinkRewrite());
        $command->setLocalizedNames($editableRootCategoryTestCaseData->getName());
        $command->setLocalizedDescriptions($editableRootCategoryTestCaseData->getDescription());
        $command->setLocalizedMetaTitles($editableRootCategoryTestCaseData->getMetaTitle());
        $command->setLocalizedMetaDescriptions($editableRootCategoryTestCaseData->getMetaDescription());
        $command->setLocalizedMetaKeywords($editableRootCategoryTestCaseData->getMetaKeywords());
        $command->setAssociatedGroupIds($editableRootCategoryTestCaseData->getGroupAssociationIds());

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I add new root category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function addNewRootCategoryWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $editableRootCategoryTestCaseData = $this->mapDataToEditableCategory($testCaseData);

        /** @var EditCategoryCommand $command */
        $command = new AddRootCategoryCommand(
            $editableRootCategoryTestCaseData->getName(),
            $editableRootCategoryTestCaseData->getLinkRewrite(),
            $editableRootCategoryTestCaseData->isActive()
        );
        $command->setLocalizedDescriptions($editableRootCategoryTestCaseData->getDescription());
        $command->setLocalizedMetaTitles($editableRootCategoryTestCaseData->getMetaTitle());
        $command->setLocalizedMetaDescriptions($editableRootCategoryTestCaseData->getMetaDescription());
        $command->setLocalizedMetaKeywords($editableRootCategoryTestCaseData->getMetaKeywords());
        $command->setAssociatedGroupIds($editableRootCategoryTestCaseData->getGroupAssociationIds());

        /** @var CategoryId $categoryIdObj */
        $categoryIdObj = $this->getCommandBus()->handle($command);
        SharedStorage::getStorage()->set($categoryReference, $categoryIdObj->getValue());
    }

    /**
     * @When I delete category :categoryReference cover image
     *
     * @param string $categoryReference
     */
    public function deleteCategoryCoverImage(string $categoryReference)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $this->getCommandBus()->handle(new DeleteCategoryCoverImageCommand($categoryId));
    }

    /**
     * @Given category :categoryReference has cover image
     *
     * @param string $categoryReference
     */
    public function categoryHasCoverImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $coverImage = $editableCategory->getCoverImage();
        ASSERT::assertNotNull($coverImage);
    }

    /**
     * @Then category :categoryReference does not have cover image
     *
     * @param string $categoryReference
     */
    public function categoryDoesNotHaveCoverImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $coverImage = $editableCategory->getCoverImage();
        ASSERT::assertNull($coverImage);
    }

    /**
     * @Given category :categoryReference has menu thumbnail image
     *
     * @param string $categoryReference
     */
    public function categoryHasMenuThumbnailImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $menuThumbnailImages = $editableCategory->getMenuThumbnailImages();
        ASSERT::assertCount(1, $menuThumbnailImages);
    }

    /**
     * @When I delete category :categoryReference menu thumbnail image
     *
     * @param string $categoryReference
     */
    public function deleteCategoryMenuThumbnailImage(string $categoryReference)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $editableCategory = $this->getEditableCategory($categoryReference);

        /** @var array $menuThumbnailImages - collection of objects returned would be better style */
        $menuThumbnailImages = $editableCategory->getMenuThumbnailImages();
        $menuThumbnailImageId = $menuThumbnailImages[0]['id'];

        $this->getCommandBus()->handle(new DeleteCategoryMenuThumbnailImageCommand($categoryId, $menuThumbnailImageId));
    }

    /**
     * @Then category :categoryReference does not have menu thumbnail image
     *
     * @param string $categoryReference
     */
    public function categoryDoesNotHaveMenuThumbnailImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $menuThumbnailImages = $editableCategory->getMenuThumbnailImages();
        ASSERT::assertCount(0, $menuThumbnailImages);
    }

    /**
     * @Given category :categoryReference is disabled
     *
     * @param $categoryReference
     */
    public function categoryIsDisabled(string $categoryReference)
    {
        $categoryIsEnabled = $this->getCategoryIsEnabled($categoryReference);
        ASSERT::assertFalse($categoryIsEnabled);
    }

    /**
     * @When I enable category :categoryReference
     *
     * @param string $categoryReference
     */
    public function enableCategory(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $this->getCommandBus()->handle(new SetCategoryIsEnabledCommand(
            $editableCategory->getId()->getValue(),
            true)
        );
    }

    /**
     * @When I disable category :categoryReference
     *
     * @param $categoryReference
     */
    public function disableCategory(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $this->getCommandBus()->handle(new SetCategoryIsEnabledCommand(
                $editableCategory->getId()->getValue(),
                false)
        );
    }

    /**
     * @Then category :categoryReference is enabled
     *
     * @param string $categoryReference
     */
    public function categoryIsEnabled(string $categoryReference)
    {
        $categoryIsEnabled = $this->getCategoryIsEnabled($categoryReference);
        ASSERT::assertTrue($categoryIsEnabled);
    }

    /**
     * @When I bulk enable categories :categoriesReferences
     *
     * @param string $categoriesReferences
     */
    public function bulkEnableCategories(string $categoriesReferences)
    {
        $categoriesReferencesArray = explode(',', $categoriesReferences);
        $categoryIds = [];
        foreach ($categoriesReferencesArray as $categoryReference) {
            $categoryIds[] = SharedStorage::getStorage()->get($categoryReference);
        }
        $this->getCommandBus()->handle(new BulkUpdateCategoriesStatusCommand($categoryIds, true));
    }

    /**
     * @When I bulk disable categories :categoriesReferences
     *
     * @param string $categoriesReferences
     */
    public function bulkDisableCategories(string $categoriesReferences)
    {
        $categoriesReferencesArray = explode(',', $categoriesReferences);
        $categoryIds = [];
        foreach ($categoriesReferencesArray as $categoryReference) {
            $categoryIds[] = SharedStorage::getStorage()->get($categoryReference);
        }
        $this->getCommandBus()->handle(new BulkUpdateCategoriesStatusCommand($categoryIds, false));
    }

    /**
     * @Given category :categoryReference in default language named :categoryName exists
     *
     * @param string $categoryReference
     * @param string $categoryName
     */
    public function assertCategoryExistsByName(string $categoryReference, string $categoryName)
    {
        $foundCategory = Category::searchByName($this->defaultLanguageId, $categoryName, true);

        if (!isset($foundCategory['name']) || $foundCategory['name'] !== $categoryName) {
            throw new RuntimeException(sprintf(
                'Category "%s" named "%s" was not found',
                $categoryReference,
                $categoryName
            ));
        }

        $this->getSharedStorage()->set($categoryReference, (int) $foundCategory['id_category']);
    }

    /**
     * @Given category ":reference" is the default one
     *
     * @param string $reference
     */
    public function assertIsDefaultCategory(string $reference): void
    {
        $defaultCategoryId = (int) Configuration::get('PS_HOME_CATEGORY');

        if (!$this->getSharedStorage()->exists($reference)) {
            throw new RuntimeException(sprintf(
                'Category referenced as "%s" was not set in sharedStorage',
                $reference
            ));
        }

        Assert::assertEquals(
            $defaultCategoryId,
            $this->getSharedStorage()->get($reference),
            'Unexpected default category'
        );
    }

    /**
     * @param CategoryForTree[] $actualCategories
     * @param array<int, array<string, string>> $expectedCategories
     * @param int $langId
     */
    private function assertCategoriesInTree(array $actualCategories, array $expectedCategories, int $langId): void
    {
        Assert::assertEquals(
            count($actualCategories),
            count($expectedCategories),
            'Unexpected categories count'
        );

        foreach ($actualCategories as $key => $category) {
            $expectedCategory = $expectedCategories[$key];
            $expectedId = $this->getSharedStorage()->get($expectedCategory['id reference']);
            $expectedChildrenCategoryIds = array_map(function (string $childCategoryReference): int {
                return $this->getSharedStorage()->get($childCategoryReference);
            }, PrimitiveUtils::castStringArrayIntoArray($expectedCategory['direct child categories']));

            $actualCategoryChildren = $category->getChildren();
            Assert::assertEquals(
                count($actualCategoryChildren),
                count($expectedChildrenCategoryIds),
                'Unexpected children categories count'
            );

            Assert::assertEquals($expectedId, $category->getCategoryId(), 'Unexpected category id');
            Assert::assertEquals([$langId => $expectedCategory['category name']], $category->getLocalizedNames(), 'Unexpected category name');

            foreach ($actualCategoryChildren as $index => $childCategory) {
                Assert::assertEquals($expectedChildrenCategoryIds[$index], $childCategory->getCategoryId());
            }
        }
    }

    /**
     * @Given category ":categoryReference" parent is category ":expectedParentReference"
     *
     * @param string $categoryReference
     * @param string $expectedParentReference
     */
    public function assertCategoryParent(string $categoryReference, string $expectedParentReference): void
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $parentId = $this->getSharedStorage()->get($expectedParentReference);

        Assert::assertEquals(
            $editableCategory->getParentId(),
            $parentId,
            'Unexpected parent category'
        );
    }

    /**
     * @param CategoryForTree[] $categoriesTree
     * @param int $parentCategoryId
     *
     * @return CategoryForTree[]
     */
    private function extractCategoriesByParent(array $categoriesTree, int $parentCategoryId): array
    {
        foreach ($categoriesTree as $category) {
            if ($category->getCategoryId() === $parentCategoryId) {
                return $category->getChildren();
            }

            $extractedChildren = $this->extractCategoriesByParent($category->getChildren(), $parentCategoryId);

            if (empty($extractedChildren)) {
                continue;
            }

            return $extractedChildren;
        }

        return [];
    }

    /**
     * @param array $testCaseData
     * @param int $categoryId
     * @param array|null $coverImage
     * @param array $subcategories
     *
     * @return EditableCategory
     */
    private function mapDataToEditableCategory(
        array $testCaseData,
        int $categoryId = self::DEFAULT_ROOT_CATEGORY_ID,
        array $subcategories = [],
        array $coverImage = null
    ): EditableCategory {
        $parentCategoryId = $this->getParentCategoryId($testCaseData);
        $groupAssociationIds = $this->getGroupAssociationIds($testCaseData);
        $isActive = PrimitiveUtils::castElementInType($testCaseData['Displayed'], PrimitiveUtils::TYPE_BOOLEAN);

        $name = [$this->defaultLanguageId => self::EMPTY_VALUE];
        if (isset($testCaseData['Name'])) {
            $name = [$this->defaultLanguageId => $testCaseData['Name']];
        }
        $description = [$this->defaultLanguageId => self::EMPTY_VALUE];
        if (isset($testCaseData['Description'])) {
            $description = [$this->defaultLanguageId => $testCaseData['Description']];
        }

        $metaTitle = [$this->defaultLanguageId => self::EMPTY_VALUE];
        if (isset($testCaseData['Meta title'])) {
            $metaTitle = [$this->defaultLanguageId => $testCaseData['Meta title']];
        }

        $metaDescription = [$this->defaultLanguageId => self::EMPTY_VALUE];
        if (isset($testCaseData['Meta description'])) {
            $metaDescription = [$this->defaultLanguageId => $testCaseData['Meta description']];
        }

        $linkRewrite = [$this->defaultLanguageId => self::EMPTY_VALUE];
        if (isset($testCaseData['Friendly URL'])) {
            $linkRewrite = [$this->defaultLanguageId => $testCaseData['Friendly URL']];
        }

        if ($parentCategoryId === null) {
            $parentCategoryId = CategoryTreeIterator::ROOT_CATEGORY_ID;
        }
        if (isset($testCaseData['Category cover image'])) {
            $coverImage = $this->pretendImageUploaded($testCaseData, $categoryId);
        }
        $menuThumbNailsImages = [];
        if (isset($testCaseData['Menu thumbnails'])) {
            $menuThumbNailsImages = $this->pretendMenuThumbnailImagesUploaded(
                $testCaseData,
                $menuThumbNailsImages,
                $categoryId
            );
        }

        //@todo: useless test. Must retrieve it from db.
        return new EditableCategory(
            new CategoryId($categoryId),
            $name,
            $isActive,
            $description,
            $parentCategoryId,
            $metaTitle,
            $metaDescription,
            [$this->defaultLanguageId => self::EMPTY_VALUE],
            $linkRewrite,
            $groupAssociationIds,
            [0 => '1'],
            $parentCategoryId === null || $parentCategoryId === 1 ? true : false,
            $coverImage,
            null,
            $menuThumbNailsImages,
            $subcategories
        );
    }

    /**
     * @param array $testCaseData
     *
     * @return int
     */
    private function getParentCategoryId(array $testCaseData)
    {
        $parentCategoryId = null;
        if (isset($testCaseData['Parent category'])) {
            /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
            $categoryTreeChoiceProvider = $this->container->get(
                'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
            $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
            $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);
        }
        if ($parentCategoryId === null) {
            $parentCategoryId = CategoryTreeIterator::ROOT_CATEGORY_ID;
        }

        return $parentCategoryId;
    }

    /**
     * @param array $testCaseData
     * @param int $categoryId
     *
     * @return string
     */
    private function pretendImageUploaded(array $testCaseData, int $categoryId): string
    {
        //@todo: refactor CategoryCoverUploader. Move uploaded file in Form handler instead of Uploader and use the uploader here in tests
        $categoryCoverImageName = $testCaseData['Category cover image'];
        $data = base64_decode(self::JPG_IMAGE_STRING);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            header('Content-Type: image/jpg');
            imagejpeg(
                $im,
                $this->psCatImgDir . $categoryId . self::JPG_IMAGE_TYPE,
                0
            );
            imagedestroy($im);
        }

        return $categoryCoverImageName;
    }

    /**
     * @param array $testCaseData
     *
     * @return array
     */
    private function getGroupAssociationIds(array $testCaseData): array
    {
        /** @var GroupByIdChoiceProvider $groupByIdChoiceProvider */
        $groupByIdChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.group_by_id_choice_provider'
        );
        $groupChoicesArray = $groupByIdChoiceProvider->getChoices();

        $groupAssociationIds = [];
        if (isset($testCaseData['Group access'])) {
            $groupAssociations = explode(',', $testCaseData['Group access']);
            foreach ($groupAssociations as $groupAssociation) {
                $groupAssociationIds[] = (int) $groupChoicesArray[$groupAssociation];
            }
        } else {
            $groupAssociationIds = [
                0 => '1',
                1 => '2',
                2 => '3',
            ];
        }

        return $groupAssociationIds;
    }

    /**
     * @param string $categoryReference
     *
     * @return EditableCategory
     */
    private function getEditableCategory(string $categoryReference): EditableCategory
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));

        return $editableCategory;
    }

    /**
     * @param string $categoryReference
     *
     * @return mixed
     */
    private function getCategoryIsEnabled(string $categoryReference)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $categoryIsEnabled = $this->getQueryBus()->handle(new GetCategoryIsEnabled($categoryId));

        return $categoryIsEnabled;
    }

    /**
     * @param array $testCaseData
     * @param array $menuThumbNailsImages
     * @param int $categoryId
     *
     * @return array
     */
    private function pretendMenuThumbnailImagesUploaded(
        array $testCaseData,
        array $menuThumbNailsImages,
        int $categoryId
    ): array {
        $data = base64_decode(self::JPG_IMAGE_STRING);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            header('Content-Type: image/jpg');
            imagejpeg(
                $im,
                $this->psCatImgDir . $categoryId . '-' . self::THUMB0 . self::JPG_IMAGE_TYPE,
                0
            );
            imagedestroy($im);
        }
        $menuThumbnailImage = $testCaseData['Menu thumbnails'];
        $menuThumbNailsImages[] = $menuThumbnailImage;

        return $menuThumbNailsImages;
    }
}
