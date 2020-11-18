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

use Context;
use Category;
use Tools;

class CategoryFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Category[]
     */
    protected $categories = [];

    /**
     * @Given /^there is a category named "(.+)"$/
     */
    public function createCategory($categoryName)
    {
        $idLang = (int) Context::getContext()->language->id;
        $category = new Category();
        $category->name = [$idLang => $categoryName];
        $category->link_rewrite = [$idLang => Tools::link_rewrite($categoryName)];
        $category->add();
        $this->categories[$categoryName] = $category;
    }

    /**
     * @param $categoryName
     */
    public function checkCategoryWithNameExists($categoryName)
    {
        $this->checkFixtureExists($this->categories, 'Category', $categoryName);
    }

    /**
     * @param $categoryName
     *
     * @return Category
     */
    public function getCategoryWithName($categoryName)
    {
        return $this->categories[$categoryName];
    }

    /**
     * @AfterScenario
     */
    public function cleanCategoryFixtures()
    {
        foreach ($this->categories as $category) {
            $category->delete();
        }

        $this->categories = [];
    }
}
