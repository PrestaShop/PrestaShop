<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CategoryTreeChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\BulkDeleteCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\DeleteCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\UpdateCategoryPositionCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\CategoryTreeIterator;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
    const EMPTY_VALUE = '';

    const CATEGORY_POSITION_WAYS_MAP = [
        0 => 'Up',
        1 => 'Down',
    ];

    /** @var ContainerInterface */
    private $container;
    /** @var int */
    private $defaultLanguageId;

    /**
     * CategoryFeatureContext constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
        $this->defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');
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
            array($this->defaultLanguageId => $testCaseData['Name']),
            array($this->defaultLanguageId => $testCaseData['Friendly URL']),
            PrimitiveUtils::castElementInType($testCaseData['Displayed'], PrimitiveUtils::TYPE_BOOLEAN),
            $parentCategoryId
        ));

        SharedStorage::getStorage()->set($categoryReference, $categoryIdObject->getValue());
    }

    /**
     * @When I add new category :reference with specified properties
     */
    public function addCategoryWithSpecifiedProperties($reference)
    {
        $properties = SharedStorage::getStorage()->get(sprintf('%s_properties', $reference));
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        $command = new AddCategoryCommand(
            [$defaultLanguageId => $properties['name']],
            [$defaultLanguageId => $properties['link_rewrite']],
            $properties['is_enabled'],
            $properties['parent_category_id']
        );
        $command->setLocalizedDescriptions([$defaultLanguageId => $properties['description']]);
        $command->setAssociatedGroupIds($properties['group_ids']);
        $command->setLocalizedMetaTitles([$defaultLanguageId => $properties['meta_title']]);
        $command->setLocalizedMetaDescriptions([$defaultLanguageId => $properties['meta_description']]);

        /** @var CategoryId $categoryIdObject */
        $categoryIdObject = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, $categoryIdObject->getValue());
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
        $categoryId = SharedStorage::getStorage()->get($categoryReference);

        /** @var EditableCategory $editableCategory */
        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));
        // coverImage array has unique properties generated based on time
        $coverImage = $editableCategory->getCoverImage();
        $subCategories = $editableCategory->getSubCategories();

        /** @var EditableCategory $expectedEditableCategory */
        $expectedEditableCategory = $this->mapDataToEditableCategory(
            $testCaseData,
            $categoryId,
            $subCategories,
            $coverImage
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
     * @When I update category :categoryReference position with following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function updateCategoryPositionWithFollowingDetails(string $categoryReference, TableNode $table)
    {
        /** @var array $testCaseData */
        $testCaseData = $table->getRowsHash();

        $categoryId = SharedStorage::getStorage()->get($categoryReference);

        /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
        $categoryTreeChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
        $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
        $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);

        $wayId = array_flip(self::CATEGORY_POSITION_WAYS_MAP)[$testCaseData['Way']];
        $positionsArray = explode(',', $testCaseData['Positions']);

        $this->getCommandBus()->handle(new UpdateCategoryPositionCommand(
            $categoryId,
            $parentCategoryId,
            $wayId,
            $positionsArray,
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
        $command = new EditCategoryCommand($categoryId);
        $command->setIsActive($editableRootCategoryTestCaseData->isActive());
        $command->setLocalizedLinkRewrites($editableRootCategoryTestCaseData->getLinkRewrite());
        $command->setLocalizedNames($editableRootCategoryTestCaseData->getName());
        $command->setLocalizedDescriptions($editableRootCategoryTestCaseData->getDescription());
        $command->setLocalizedMetaTitles($editableRootCategoryTestCaseData->getMetaTitle());
        $command->setLocalizedMetaDescriptions($editableRootCategoryTestCaseData->getMetaDescription());
        $command->setLocalizedMetaKeywords($editableRootCategoryTestCaseData->getMetaKeywords());
        $command->setAssociatedGroupIds($editableRootCategoryTestCaseData->getGroupAssociationIds());

        $this->getCommandBus()->handle($command);

        $this->getCommandBus()->handle(new EditRootCategoryCommand($categoryId));
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
        int $categoryId,
        array $subcategories = [],
        array $coverImage = null
    ): EditableCategory {
        $parentCategoryId = null;
        if (isset($testCaseData['Parent category'])) {
            /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
            $categoryTreeChoiceProvider = $this->container->get(
                'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
            $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
            $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);
        }

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
            $groupAssociationIds = array(
                0 => '1',
                1 => '2',
                2 => '3',
            );
        }

        $isActive = PrimitiveUtils::castElementInType($testCaseData['Displayed'], PrimitiveUtils::TYPE_BOOLEAN);

        $name = array($this->defaultLanguageId => self::EMPTY_VALUE);
        if (isset($testCaseData['Name'])) {
            $name = array($this->defaultLanguageId => $testCaseData['Name']);
        }
        $description = array($this->defaultLanguageId => self::EMPTY_VALUE);
        if (isset($testCaseData['Description'])) {
            $description = array($this->defaultLanguageId => $testCaseData['Description']);
        }

        $metaTitle = array($this->defaultLanguageId => self::EMPTY_VALUE);
        if (isset($testCaseData['Meta title'])) {
            $metaTitle = array($this->defaultLanguageId => $testCaseData['Meta title']);
        }

        $metaDescription = array($this->defaultLanguageId => self::EMPTY_VALUE);
        if (isset($testCaseData['Meta description'])) {
            $metaDescription = array($this->defaultLanguageId => $testCaseData['Meta description']);
        }

        $linkRewrite = array($this->defaultLanguageId => self::EMPTY_VALUE);
        if (isset($testCaseData['Friendly URL'])) {
            $linkRewrite = array($this->defaultLanguageId => $testCaseData['Friendly URL']);
        }

        if ($parentCategoryId === null) {
            $parentCategoryId = CategoryTreeIterator::ROOT_CATEGORY_ID;
        }

        return new EditableCategory(
            new CategoryId($categoryId),
            $name,
            $isActive,
            $description,
            $parentCategoryId,
            $metaTitle,
            $metaDescription,
            array($this->defaultLanguageId => self::EMPTY_VALUE),
            $linkRewrite,
            $groupAssociationIds,
            array(0 => '1'),
            $parentCategoryId === null || $parentCategoryId === 1 ? true : false,
            $coverImage,
            null,
            [],
            $subcategories
        );
    }
}
