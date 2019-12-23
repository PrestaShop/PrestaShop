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
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class CategoryFeatureContext extends AbstractDomainFeatureContext
{
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
     * @todo finish
     * @Then category :categoryReference should have following details:
     *
     * @param string $categoryReference
     * @param TableNode $table
     */
    public function categoryShouldHaveFollowingDetails(string $categoryReference, TableNode $table)
    {
        $categoryId = SharedStorage::getStorage()->get($categoryReference);
        $testCaseData = $table->getRowsHash();

//            $isActive,
//            array $description,
//                $parentId,
//                array $metaTitle,
//                    array $metaDescription,
//                        array $metaKeywords,
//                            array $linkRewrite,
//                                array $groupAssociationIds,
//                                    array $shopAssociationIds,
//                                        $isRootCategory,
//                                        $coverImage = null,
//                                        $thumbnailImage = null,
//                                        array $menuThumbnailImages = [],
//                                            array $subCategories = []

//      | Displayed        | true                      |
//      | Parent category  | Home Accessories          |
//      | Description      | Best PC parts             |
//      | Meta title       | PC parts meta title       |
//      | Meta description | PC parts meta description |
//      | Friendly URL     | pc-parts                  |
//      | Group access     | Customer,Guest,Visitor    |

        $editableCategory = new EditableCategory(
            new CategoryId($categoryId),
            array($testCaseData['Name']),

        );

        throw new PendingException();
    }


    /**
     * @When I edit category :arg1 with following details:
     */
    public function iEditCategoryWithFollowingDetails($arg1, TableNode $table)
    {
        throw new PendingException();
    }

}
