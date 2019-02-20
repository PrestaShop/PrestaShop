<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 18.16
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;


use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

final class CmsCategoriesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $nestedCategories;

    /**
     * @param array $nestedCategories
     */
    public function __construct(array $nestedCategories)
    {
        $this->nestedCategories = $nestedCategories;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices[] = $this->buildChoiceTree($this->nestedCategories);

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
            'id_cms_category' => $category['id_cms_category'],
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
