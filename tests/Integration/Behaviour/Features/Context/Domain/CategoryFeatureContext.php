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
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkUpdateCategoriesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCoverImageCommand;
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
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Integration\Behaviour\Features\Transform\StringToBoolTransformContext;
use Tests\Resources\DummyFileUploader;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
    public const JPG_IMAGE_TYPE = '.jpg';

    private const CATEGORY_POSITION_WAYS_MAP = [
        'up' => 0,
        'down' => 1,
    ];

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
        $this->addNewCategory($categoryReference, $table, false);
    }

    /**
     * @When I add new home category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function addHomeCategory(string $categoryReference, TableNode $table): void
    {
        $this->addNewCategory($categoryReference, $table, true);
    }

    /**
     * @When I edit category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function editCategory(string $categoryReference, TableNode $table)
    {
        $command = new EditCategoryCommand(SharedStorage::getStorage()->get($categoryReference));
        $this->fillEditCommandWithData($command, $table);
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
        $this->assertProperty($data, 'meta keywords', $editableCategory->getMetaKeywords());
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
     * @When /^I move category "(.*)" (up|down) to a position "(.*)"$/
     *
     * @param string $categoryReference
     */
    public function updatePosition(string $categoryReference, string $way, int $newPosition)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $parentCategoryId = $this->getEditableCategory($categoryReference)->getParentId();
        /** @var EditableCategory $parentCategory */
        $parentCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($parentCategoryId));
        // all the categories from same parent to mimic the position change in BO
        // (we can change the position in a list of categories from a certain parent)
        $siblingCategories = $parentCategory->getSubCategories();

        $idsByPositions = [];
        $previousPosition = null;
        foreach ($siblingCategories as $siblingCategoryId) {
            $siblingCategoryId = (int) $siblingCategoryId;
            $siblingCategory = $this->getCategory($siblingCategoryId);
            // organize categories by position
            $idsByPositions[(int) $siblingCategory->position] = $siblingCategoryId;

            if ($categoryId === $siblingCategoryId) {
                // find the previous position of the category that is being updated
                $previousPosition = (int) $siblingCategory->position;
            }
        }

        // find the id that was in the new position before changing it
        $previousIdInNewPosition = $idsByPositions[$newPosition];

        // switch the positions
        $idsByPositions[$newPosition] = $categoryId;
        $idsByPositions[$previousPosition] = $previousIdInNewPosition;

        $generatedPositions = [];
        foreach ($idsByPositions as $position => $id) {
            // mimic generating of positions like in list
            //@todo: the whole UpdateCategoryPositionCommand needs to be refactored, it shouldn't depend on UI
            $generatedPositions[$position] = 'tr_' . $parentCategoryId . '_' . $id;
        }

        $this->getCommandBus()->handle(new UpdateCategoryPositionCommand(
            $categoryId,
            $parentCategoryId,
            self::CATEGORY_POSITION_WAYS_MAP[$way],
            $generatedPositions,
            false
        ));
    }

    /**
     * @Then category ":categoryReference" position should be ":expectedPosition"
     *
     * @param string $categoryReference
     */
    public function assertCurrentPosition(string $categoryReference, int $expectedPosition): void
    {
        $category = $this->getCategory($this->getSharedStorage()->get($categoryReference));

        Assert::assertSame($expectedPosition, (int) $category->position);
    }

    /**
     * @When I edit home category :categoryReference with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function editHomeCategory(string $categoryReference, TableNode $table)
    {
        $command = new EditRootCategoryCommand($this->getSharedStorage()->get($categoryReference));
        $this->fillEditCommandWithData($command, $table);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @param string $categoryReference
     * @param TableNode $table
     * @param bool $isHome
     *
     * @see AddRootCategoryCommand
     *
     * Technically both commands should be filled separately,
     * but it happens to match almost all properties except $parentId, so we can reuse them here.
     * @see AddCategoryCommand
     */
    private function addNewCategory(string $categoryReference, TableNode $table, bool $isHome): void
    {
        $data = $this->localizeByRows($table);

        if ($isHome) {
            $command = new AddRootCategoryCommand(
                $data['name'],
                $data['link rewrite'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['active'])
            );
        } else {
            $command = new AddCategoryCommand(
                $data['name'],
                $data['link rewrite'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['active']),
                $this->getSharedStorage()->get($data['parent category'])
            );
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
        if (isset($data['meta keywords'])) {
            $command->setLocalizedMetaKeywords($data['meta keywords']);
        }

        /** @var CategoryId $categoryId */
        $categoryId = $this->getCommandBus()->handle($command);
        SharedStorage::getStorage()->set($categoryReference, $categoryId->getValue());
    }

    /**
     * @param EditCategoryCommand|EditRootCategoryCommand $command
     * @param TableNode $tableNode
     *
     * @see EditCategoryCommand
     * @see EditRootCategoryCommand
     *
     * Technically both commands should be filled separately,
     * but it happens to match almost all properties except $parentId, so we can reuse them here.
     *
     * If in future these commands evolves differently (which probably won't happen),
     * then don't hesitate to extract this method into 2 dedicated ones.
     */
    private function fillEditCommandWithData($command, TableNode $tableNode): void
    {
        $supportedCommands = [EditCategoryCommand::class, EditRootCategoryCommand::class];

        if (!in_array(get_class($command), $supportedCommands, true)) {
            throw new RuntimeException('Unsupported command provided for filling the data in test');
        }

        $data = $this->localizeByRows($tableNode);

        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['link rewrite'])) {
            $command->setLocalizedLinkRewrites($data['link rewrite']);
        }
        if (isset($data['active'])) {
            $command->setIsActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
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
        if (isset($data['meta keywords'])) {
            $command->setLocalizedMetaKeywords($data['meta keywords']);
        }
        if ($command instanceof EditCategoryCommand && isset($data['parent category'])) {
            $command->setParentCategoryId($this->getSharedStorage()->get($data['parent category']));
        }
    }

    /**
     * @Then image ":imageReference" should not exist
     *
     * @param string $imageReference
     */
    public function assertFileDoesNotExist(string $imageReference): void
    {
        Assert::assertFalse(file_exists($this->getSharedStorage()->get($imageReference)));
    }

    /**
     * @When I delete cover image for category ":categoryReference"
     *
     * @param string $categoryReference
     */
    public function deleteCategoryCoverImage(string $categoryReference)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $this->getCommandBus()->handle(new DeleteCategoryCoverImageCommand($categoryId));
    }

    /**
     * @Then category ":categoryReference" should have a cover image
     *
     * @param string $categoryReference
     */
    public function categoryHasCoverImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        $coverImage = $editableCategory->getCoverImage();
        Assert::assertNotNull($coverImage);
    }

    /**
     * @Then category :categoryReference should not have a cover image
     *
     * @param string $categoryReference
     */
    public function assertCategoryHasNoCoverImage(string $categoryReference): void
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        Assert::assertNull($editableCategory->getCoverImage());
    }

    /**
     * @Then category :categoryReference should not have a thumbnail image
     *
     * @param string $categoryReference
     */
    public function assertCategoryHasNoThumbnailImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        Assert::assertNull($editableCategory->getThumbnailImage());
    }

    /**
     * @Then category :categoryReference should have a thumbnail image
     *
     * @param string $categoryReference
     */
    public function assertCategoryHasThumbnailImage(string $categoryReference)
    {
        $editableCategory = $this->getEditableCategory($categoryReference);
        Assert::assertNotNull($editableCategory->getThumbnailImage());
    }

    /**
     * @Given /^category "(.*)" is (enabled|disabled)$/
     *
     * Status type "enabled|disabled" should be converted by transform context. @see StringToBoolTransformContext
     *
     * @param string $categoryReference
     * @param bool $expectedStatus
     */
    public function assertCategoryStatus(string $categoryReference, bool $expectedStatus): void
    {
        /** @var bool $isEnabled */
        $isEnabled = $this->getQueryBus()->handle(new GetCategoryIsEnabled($this->getSharedStorage()->get($categoryReference)));
        Assert::assertSame($expectedStatus, $isEnabled);
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
     * @When /^I bulk (enable|disable) categories "(.*)"$/
     *
     * @param bool $enable
     * @param string $categoryReferences
     */
    public function bulkUpdateCategoriesStatus(bool $enable, string $categoryReferences)
    {
        $this->getCommandBus()->handle(
            new BulkUpdateCategoriesStatusCommand($this->referencesToIds($categoryReferences), $enable)
        );
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
     * @param string $reference
     * @todo: should start naming "home" everywhere instead of "default".
     * @Given category ":reference" is the default one
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
     * @When I upload cover image ":imageReference" named ":fileName" to category ":categoryReference"
     *
     * @return string
     */
    public function uploadCoverImage(string $imageReference, string $fileName, string $categoryReference): string
    {
        $categoryId = $this->getSharedStorage()->get($categoryReference);

        return $this->uploadImage(
            $imageReference,
            $fileName,
            $this->psCatImgDir . $categoryId . self::JPG_IMAGE_TYPE
        );
    }

    /**
     * @When I upload thumbnail image ":imageReference" named ":fileName" to category ":categoryReference"
     *
     * @return string
     */
    public function uploadThumbnailImage(string $imageReference, string $fileName, string $categoryReference): string
    {
        $categoryId = $this->getSharedStorage()->get($categoryReference);

        return $this->uploadImage(
            $imageReference,
            $fileName,
            $this->psCatImgDir . $categoryId . '-small_default' . self::JPG_IMAGE_TYPE
        );
    }

    /**
     * @todo: this doesn't actually test the upload,
     *        it only mimics it, so we can later assert EditableCategory and test image deletion.
     *        Whole image upload is not easily testable due to these reasons:
     *          - image uploads depends on HTTP request (move_uploaded_file is used in uploader service)
     *          - EditableCategory images (all types) are regenerated thumbnails with timestamps, so it is complicated to assert their value
     *
     * @param string $imageReference
     * @param string $fileName
     * @param string $destinationPath
     *
     * @return string
     */
    private function uploadImage(string $imageReference, string $fileName, string $destinationPath): string
    {
        $sourcePath = DummyFileUploader::getDummyFilesPath() . $fileName;

        if (!copy($sourcePath, $destinationPath) || !file_exists($destinationPath)) {
            throw new RuntimeException('Failed to upload category image file');
        }

        $this->getSharedStorage()->set($imageReference, $destinationPath);

        return $fileName;
    }

    /**
     * @param string $categoryReference
     *
     * @return EditableCategory
     */
    private function getEditableCategory(string $categoryReference): EditableCategory
    {
        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(
            new GetCategoryForEditing(SharedStorage::getStorage()->get($categoryReference))
        );

        return $editableCategory;
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

    /**
     * @param int $categoryId
     *
     * @return Category
     */
    private function getCategory(int $categoryId): Category
    {
        // There is no position in EditableCategory class, so we ensure it is correct by loading legacy ObjectModel
        $category = new Category($categoryId);

        if ((int) $category->id !== $categoryId) {
            throw new RuntimeException(sprintf('Failed to load category with id %d', $categoryId));
        }

        return $category;
    }
}
