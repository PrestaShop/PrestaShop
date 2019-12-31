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

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Configuration;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CategoryTreeChoiceProvider;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use Psr\Container\ContainerInterface;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\CategoryTreeIterator;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
    /** @var ContainerInterface */
    private $container;

    /**
     * CategoryFeatureContext constructor.
     */
    public function __construct()
    {
        $this->container = $this->getContainer();
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
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $testCaseData = $table->getRowsHash();

        /** @var EditableCategory $expectedEditableCategory */
        $expectedEditableCategory = $this->mapDataToEditableCategory($testCaseData, $categoryId);

        $editableCategory = $this->getQueryBus()->handle(new GetCategoryForEditing($categoryId));
        PHPUnit_Framework_Assert::assertEquals($expectedEditableCategory, $editableCategory);
    }

    /**
     * @When I edit category :arg1 with following details:
     */
    public function iEditCategoryWithFollowingDetails($arg1, TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @param array $testCaseData
     * @param $categoryId
     *
     * @return EditableCategory
     */
    private function mapDataToEditableCategory(array $testCaseData, $categoryId): EditableCategory
    {
        /** @var CategoryTreeChoiceProvider $categoryTreeChoiceProvider */
        $categoryTreeChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.category_tree_choice_provider');
        $categoryTreeIterator = new CategoryTreeIterator($categoryTreeChoiceProvider);
        $parentCategoryId = $categoryTreeIterator->getCategoryId($testCaseData['Parent category']);

        /** @var GroupByIdChoiceProvider $groupByIdChoiceProvider */
        $groupByIdChoiceProvider = $this->container->get(
            'prestashop.adapter.form.choice_provider.group_by_id_choice_provider'
        );
        $groupChoicesArray = $groupByIdChoiceProvider->getChoices();

        $groupAssociations = explode(',', $testCaseData['Group access']);
        $groupAssociationIds = [];
        foreach ($groupAssociations as $groupAssociation) {
            $groupAssociationIds[] = (int) $groupChoicesArray[$groupAssociation];
        }

        $isActive = PrimitiveUtils::castElementInType($testCaseData['Displayed'], PrimitiveUtils::TYPE_BOOLEAN);

        return new EditableCategory(
            new CategoryId($categoryId),
            array(1 => $testCaseData['Name']),
            $isActive,
            array(1 => $testCaseData['Description']),
            $parentCategoryId,
            array(1 => $testCaseData['Meta title']),
            array(1 => $testCaseData['Meta description']),
            array(1 => ''),
            array(1 => $testCaseData['Friendly URL']),
            $groupAssociationIds,
            array(0 => '1'),
            false
        );
    }
}
