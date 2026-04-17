<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Category;
use Context;
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
        $category->link_rewrite = [$idLang => Tools::str2url($categoryName)];
        $category->add();
        $this->categories[$categoryName] = $category;
    }

    /**
     * @param string $categoryName
     */
    public function checkCategoryWithNameExists(string $categoryName): void
    {
        $this->checkFixtureExists($this->categories, 'Category', $categoryName);
    }

    /**
     * @param string $categoryName
     *
     * @return Category
     */
    public function getCategoryWithName($categoryName): Category
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
