<?php

namespace Tests\Integration\Behaviour\Features\Context\Util;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CategoryTreeChoiceProvider;

class CategoryTreeIterator
{
    public const ROOT_CATEGORY_ID = 1;

    public $categoryTreeChoiceProvider;

    /**
     * CategoryTreeIterator constructor.
     *
     * @param $categoryTreeChoiceProvider
     */
    public function __construct($categoryTreeChoiceProvider)
    {
        $this->categoryTreeChoiceProvider = $categoryTreeChoiceProvider;
    }

    public function getCategoryId(string $categoryName)
    {
        $categoryTreeChoicesArray = $this->categoryTreeChoiceProvider->getChoices();

        return $this->getCategoryNodeId($categoryName, $categoryTreeChoicesArray);
    }

    /**
     * @param string $categoryName
     * @param array $nodes
     *
     * @return int
     */
    private function getCategoryNodeId(string $categoryName, array $nodes)
    {
        $i = 0;
        foreach ($nodes as $node) {
            ++$i;
            if ($node['name'] == $categoryName) {
                return (int) $node['id_category'];
            }
            if (isset($node['children'])) {
                $categoryId = (int) $this->getCategoryNodeId($categoryName, $node['children']);
                if ($categoryId) {
                    return $categoryId;
                }
            }
            if (count($nodes) === $i) {
                return null;
            }
        }
    }
}
