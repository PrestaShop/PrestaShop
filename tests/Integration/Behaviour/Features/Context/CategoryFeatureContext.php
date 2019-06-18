<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context;

use Category;
use Configuration;
use Group;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CategoryFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    public function __construct()
    {
        $this->defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
    }

    /**
     * @Given /^I specify "(.+)" "(.+)" for new category "(.+)"$/
     */
    public function specifyProperty($property, $value, $reference)
    {
        $propertiesMap = [
            'name' => 'name',
            'displayed' => 'active',
            'parent category' => 'parent_category_id',
            'description' => 'description',
            'meta title' => 'meta_title',
            'meta description' => 'meta_description',
            'friendly url' => 'link_rewrite',
        ];

        if (!isset($propertiesMap[$property])) {
            throw new RuntimeException(sprintf('Property "%s" is not defined', $property));
        }

        $propertiesKey = sprintf('%s_properties', $reference);
        $properties = SharedStorage::getStorage()->getWithDefault($propertiesKey, []);

        if ('parent_category_id' === $propertiesMap[$property]) {
            $categoriesMap = [
                'Home Accessories' => 8,
            ];

            $value = $categoriesMap[$value];
        }

        $properties[$propertiesMap[$property]] = $value;

        SharedStorage::getStorage()->set($propertiesKey, $properties);
    }

    /**
     * @Given /^I specify displayed to be "(enabled|disabled)" for new category "(.+)"$/
     */
    public function specifyDisplayedProperty($value, $reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);
        $properties = SharedStorage::getStorage()->getWithDefault($propertiesKey, []);

        $properties['is_enabled'] = 'enabled' === $value;

        SharedStorage::getStorage()->set($propertiesKey, $properties);
    }

    /**
     * @Given /^I specify group access for "(.+)" for new category "(.+)"$/
     */
    public function specifyGroupAccess($value, $reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);
        $properties = SharedStorage::getStorage()->getWithDefault($propertiesKey, []);

        $groupsMap = [
            'Visitor' => 1,
            'Guest' => 2,
            'Customer' => 3,
        ];

        $groupNames = explode(',', $value);
        $groupIds = [];

        foreach ($groupNames as $groupName) {
            $groupIds[] = $groupsMap[$groupName];
        }

        $properties['group_ids'] = $groupIds;

        SharedStorage::getStorage()->set($propertiesKey, $properties);
    }

    /**
     * @Then /^category "(.+)" "(.+)" should be "(.+)"$/
     */
    public function assertProperty($reference, $property, $value)
    {
        $propertyMap = [
            'name' => 'name',
            'description' => 'description',
            'meta title' => 'meta_title',
            'meta description' => 'meta_description',
            'meta keywords' => 'meta_keywords',
            'friendly url' => 'link_rewrite',
        ];

        /** @var Category $category */
        $category = SharedStorage::getStorage()->get($reference);
        $defaultLanguageId = Configuration::get('PS_LANG_DEFAULT');

        if ($category->{$propertyMap[$property]}[$defaultLanguageId] !== $value) {
            throw new RuntimeException(
                sprintf('Category "%s" "%s" was expected to be "%s"', $reference, $property, $value)
            );
        }
    }

    /**
     * @Then /^category "(.+)" should be "(displayed|hidden)"$/
     */
    public function assertDisplayedProperty($reference, $value)
    {
        $isDisplayed = 'displayed' === $value;

        /** @var Category $category */
        $category = SharedStorage::getStorage()->get($reference);

        if ((bool) $category->active !== $isDisplayed) {
            throw new RuntimeException(sprintf('Category "%s" was expected to be "%s"', $reference, $value));
        }
    }

    /**
     * @Then category :reference parent category should be :categoryName
     */
    public function assertParentCategory($reference, $categoryName)
    {
        /** @var Category $category */
        $category = SharedStorage::getStorage()->get($reference);

        $parentCategory = new Category($category->id_parent);

        if ($parentCategory->name[$this->defaultLanguageId] !== $categoryName) {
            throw new RuntimeException(
                sprintf('Category "%s" parent was expected to be "%s"', $reference, $categoryName)
            );
        }
    }

    /**
     * @Then category :reference group access should be for :groupAccess
     */
    public function assertGroupAccess($reference, $groupAccess)
    {
        $groupNames = PrimitiveUtils::castStringArrayIntoArray($groupAccess);
        $categoryGroupNames = [];

        /** @var Category $category */
        $category = SharedStorage::getStorage()->get($reference);
        $groupIds = $category->getGroups();

        foreach ($groupIds as $groupId) {
            $group = new Group($groupId);

            $categoryGroupNames[] = $group->name[$this->defaultLanguageId];
        }

        if (!empty(array_diff($groupNames, $categoryGroupNames))
            || !empty(array_diff($categoryGroupNames, $groupNames))
        ) {
            throw new RuntimeException(
                sprintf(
                    'Category "%s" was expected to have "%s" group access, but has "%s" instead.',
                    $reference,
                    $groupAccess,
                    implode(',', $categoryGroupNames)
                )
            );
        }
    }
}
