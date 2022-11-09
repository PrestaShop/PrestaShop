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
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoriesTree;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryIsEnabled;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\CategoryForTree;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use RuntimeException;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\CategoryTreeIterator;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
    public const EMPTY_VALUE = '';
    public const DEFAULT_ROOT_CATEGORY_ID = 1;
    public const THUMB0 = '0_thumb';

    public const CATEGORY_POSITION_WAYS_MAP = [
        0 => 'Up',
        1 => 'Down',
    ];

    /** @var ContainerInterface */
    private $container;
    /** @var int */
    private $defaultLanguageId;
    /** @var string */
    private $psCatImgDir;

    private const PROPERTY_TYPE_BASIC = 0;
    private const PROPERTY_TYPE_REFERENCE = 1;
    private const PROPERTY_TYPE_REFERENCE_ARRAY = 2;
    private const PROPERTY_TYPE_BOOL = 3;

    /**
     * CategoryFeatureContext constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
        $this->defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
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
        $categoriesTree = $this->getQueryBus()->handle(new GetCategoriesTree($langId, $this->getDefaultShopId()));

        Assert::assertNotEmpty($categoriesTree, 'Categories tree is empty');

        $this->assertCategoriesInTree($categoriesTree, $tableNode->getColumnsHash());
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
        Category::resetStaticCache();
        $langId = Language::getIdByIso($langIso);
        $categoriesTree = $this->getQueryBus()->handle(new GetCategoriesTree($langId, $this->getDefaultShopId()));

        Assert::assertNotEmpty($categoriesTree, 'Categories tree is empty');

        $parentCategoryId = $this->getSharedStorage()->get($parentReference);
        $actualCategories = $this->extractCategoriesByParent($categoriesTree, $parentCategoryId);

        $this->assertCategoriesInTree($actualCategories, $tableNode->getColumnsHash());
    }

    /**
     * @When I add new category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function addCategory(string $categoryReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $command = new AddCategoryCommand(
            $data['name'],
            $data['link rewrite'],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['active']),
            $this->getSharedStorage()->get($data['parent category'])
        );

        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }
        if (isset($data['meta description'])) {
            $command->setLocalizedMetaDescriptions($data['meta description']);
        }
        if (isset($data['meta title'])) {
            $command->setLocalizedMetaTitles($data['meta title']);
        }
        if (isset($data['active'])) {
            $command->setIsActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['additional description'])) {
            $command->setLocalizedAdditionalDescriptions($data['additional description']);
        }
        if (isset($data['group access'])) {
            $command->setAssociatedGroupIds($this->referencesToIds($data['group access']));
        }
        if (isset($data['associated shops'])) {
            $command->setAssociatedShopIds($this->referencesToIds($data['associated shops']));
        }

        /** @var CategoryId $categoryId */
        $categoryId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($categoryReference, $categoryId->getValue());
    }

    /**
     * @When I edit category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function editCategory(string $categoryReference, TableNode $table)
    {
        $data = $this->localizeByRows($table);
        $command = new EditCategoryCommand(SharedStorage::getStorage()->get($categoryReference));

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['link rewrite'])) {
            $command->setLocalizedLinkRewrites($data['link rewrite']);
        }
        if (isset($data['active'])) {
            $command->setIsActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['parent category'])) {
            $command->setParentCategoryId($this->getSharedStorage()->get($data['parent category']));
        }
        if (isset($data['description'])) {
            $command->setLocalizedDescriptions($data['description']);
        }
        if (isset($data['meta description'])) {
            $command->setLocalizedMetaDescriptions($data['meta description']);
        }
        if (isset($data['meta title'])) {
            $command->setLocalizedMetaTitles($data['meta title']);
        }
        if (isset($data['additional description'])) {
            $command->setLocalizedAdditionalDescriptions($data['additional description']);
        }
        if (isset($data['group access'])) {
            $command->setAssociatedGroupIds($this->referencesToIds($data['group access']));
        }
        if (isset($data['associated shops'])) {
            $command->setAssociatedShopIds($this->referencesToIds($data['associated shops']));
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then category :categoryReference should have following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function assertEditableCategory(string $categoryReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $editableCategory = $this->getEditableCategory($categoryReference);

        $this->assertProperty($data, 'name', $editableCategory->getName());
        $this->assertProperty($data, 'link rewrite', $editableCategory->getLinkRewrite());
        $this->assertProperty($data, 'active', $editableCategory->isActive(), self::PROPERTY_TYPE_BOOL);
        $this->assertProperty($data, 'parent category', $editableCategory->getParentId(), self::PROPERTY_TYPE_REFERENCE);
        $this->assertProperty($data, 'description', $editableCategory->getDescription());
        $this->assertProperty($data, 'meta description', $editableCategory->getMetaDescription());
        $this->assertProperty($data, 'meta title', $editableCategory->getMetaTitle());
        $this->assertProperty($data, 'additional description', $editableCategory->getAdditionalDescription());
        $this->assertProperty($data, 'group access', $editableCategory->getGroupAssociationIds(), self::PROPERTY_TYPE_REFERENCE_ARRAY);
        $this->assertProperty($data, 'associated shops', $editableCategory->getShopAssociationIds(), self::PROPERTY_TYPE_REFERENCE_ARRAY);
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
     * @When I edit home category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function editHomeCategoryWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        $categoryId = $this->getSharedStorage()->get($categoryReference);
        $testCaseData = $table->getRowsHash();
        $editableRootCategoryTestCaseData = $this->mapDataToEditableCategory($testCaseData, $categoryId);

        /** @var EditCategoryCommand $command */
        $command = new EditRootCategoryCommand($categoryId);
        $command->setIsActive($editableRootCategoryTestCaseData->isActive());
        $command->setLocalizedLinkRewrites($editableRootCategoryTestCaseData->getLinkRewrite());
        $command->setLocalizedNames($editableRootCategoryTestCaseData->getName());
        $command->setLocalizedDescriptions($editableRootCategoryTestCaseData->getDescription());
        $command->setLocalizedAdditionalDescriptions($editableRootCategoryTestCaseData->getAdditionalDescription());
        $command->setLocalizedMetaTitles($editableRootCategoryTestCaseData->getMetaTitle());
        $command->setLocalizedMetaDescriptions($editableRootCategoryTestCaseData->getMetaDescription());
        $command->setLocalizedMetaKeywords($editableRootCategoryTestCaseData->getMetaKeywords());
        $command->setAssociatedGroupIds($editableRootCategoryTestCaseData->getGroupAssociationIds());

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I add new home category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function addNewHomeCategoryWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();
        $editableRootCategoryTestCaseData = $this->mapDataToEditableCategory($testCaseData);

        $command = new AddRootCategoryCommand(
            $editableRootCategoryTestCaseData->getName(),
            $editableRootCategoryTestCaseData->getLinkRewrite(),
            $editableRootCategoryTestCaseData->isActive()
        );
        $command->setLocalizedDescriptions($editableRootCategoryTestCaseData->getDescription());
        $command->setLocalizedAdditionalDescriptions($editableRootCategoryTestCaseData->getAdditionalDescription());
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
        Assert::assertCount(0, $menuThumbnailImages);
    }

    /**
     * @Given category :categoryReference is disabled
     *
     * @param string $categoryReference
     */
    public function categoryIsDisabled(string $categoryReference): void
    {
        $categoryIsEnabled = $this->getCategoryIsEnabled($categoryReference);
        Assert::assertFalse($categoryIsEnabled);
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
     * @param string $categoryReference
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
        Assert::assertTrue($categoryIsEnabled);
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
     * NOTE: It is the actual ROOT category (the one with no parents), not the HOME category.
     *
     * @Given category ":categoryReference" is the root category and it cannot be edited
     */
    public function assertRootCategoryIsNotEditable(string $categoryReference): void
    {
        Assert::assertSame(
            $this->getSharedStorage()->get($categoryReference),
            (int) Configuration::get('PS_ROOT_CATEGORY')
        );

        try {
            $this->getEditableCategory($categoryReference);

            throw new RuntimeException(sprintf('%s exception was expected', CannotEditRootCategoryException::class));
        } catch (CannotEditRootCategoryException $e) {
            // this is expected. We want to make sure that root category cannot be edited.
        }
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
     * @Given category ":categoryReference" is set as the home category for shop ":shopReference"
     *
     * @param string $categoryReference
     * @param string $shopReference
     */
    public function assertIsHomeCategoryForShop(string $categoryReference, string $shopReference): void
    {
        if (!$this->getSharedStorage()->exists($shopReference)) {
            throw new RuntimeException(sprintf(
                'Shop referenced as "%s" was not set in sharedStorage',
                $categoryReference
            ));
        }

        if (!$this->getSharedStorage()->exists($categoryReference)) {
            throw new RuntimeException(sprintf(
                'Category referenced as "%s" was not set in sharedStorage',
                $categoryReference
            ));
        }

        $shopId = (int) $this->getSharedStorage()->get($shopReference);
        $shop = new Shop($shopId);
        if ((int) $shop->id !== $shopId) {
            throw new RuntimeException(sprintf(
                'Failed to load shop with id %d, referenced as %s',
                $shopId,
                $shopReference
            ));
        }

        Assert::assertSame(
            (int) $shop->id_category,
            $this->getSharedStorage()->get($categoryReference),
            sprintf('Unexpected default category for shop %s', $shopReference)
        );
    }

    /**
     * @param CategoryForTree[] $actualCategories
     * @param array<int, array<string, string>> $expectedCategories
     */
    private function assertCategoriesInTree(array $actualCategories, array $expectedCategories): void
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
            Assert::assertEquals($expectedCategory['category name'], $category->getName(), 'Unexpected category name');
            Assert::assertEquals($expectedCategory['display name'], $category->getDisplayName(), 'Unexpected category display name');

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

    /**
     * @param array<string, mixed> $localizedData
     * @param string $index
     * @param $actualValue
     * @param int $type
     */
    private function assertProperty(array $localizedData, string $index, $actualValue, int $type = self::PROPERTY_TYPE_BASIC): void
    {
        if (!isset($localizedData[$index])) {
            return;
        }

        switch ($type) {
            case self::PROPERTY_TYPE_REFERENCE:
                $expectedValue = $this->getSharedStorage()->get($localizedData[$index]);
                break;
            case self::PROPERTY_TYPE_REFERENCE_ARRAY:
                $expectedValue = $this->referencesToIds($localizedData[$index]);
                break;
            case self::PROPERTY_TYPE_BOOL:
                $expectedValue = PrimitiveUtils::castStringBooleanIntoBoolean($localizedData[$index]);
                break;
            default:
                $expectedValue = $localizedData[$index];
                break;
        }

        Assert::assertSame(
            $expectedValue,
            $actualValue,
            sprintf('Unexpected %s', $index)
        );
    }
}
