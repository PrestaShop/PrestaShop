<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CategoryTreeChoiceProvider provides categories as tree choices.
 */
final class CategoryTreeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var CategoryDataProvider
     */
    private $categoryDataProvider;

    /**
     * @var int
     */
    private $contextShopRootCategoryId;

    /**
     * @var bool
     */
    private $enabledCategoriesOnly;

    /**
     * @param CategoryDataProvider $categoryDataProvider
     * @param int $contextShopRootCategoryId
     * @param bool $enabledCategoriesOnly
     */
    public function __construct(CategoryDataProvider $categoryDataProvider, $contextShopRootCategoryId, $enabledCategoriesOnly = false)
    {
        $this->categoryDataProvider = $categoryDataProvider;
        $this->contextShopRootCategoryId = $contextShopRootCategoryId;
        $this->enabledCategoriesOnly = $enabledCategoriesOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $categories = $this->categoryDataProvider->getNestedCategories($this->contextShopRootCategoryId, false, $this->enabledCategoriesOnly);
        $choices = [];

        foreach ($categories as $category) {
            $choices[] = $this->buildChoiceTree($category);
        }

        return $choices;
    }

    /**
     * @param array $category
     *
     * @return array
     */
    private function buildChoiceTree(array $category)
    {
        $tree = [
            'id_category' => $category['id_category'],
            'name' => $category['name'],
        ];

        if (isset($category['children'])) {
            foreach ($category['children'] as $childCategory) {
                $tree['children'][] = $this->buildChoiceTree($childCategory);
            }
        }

        return $tree;
    }
}
