<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Util;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CategoryTreeChoiceProvider;

class CategoryTreeIterator
{
    public const ROOT_CATEGORY_ID = 1;

    /**
     * @var CategoryTreeChoiceProvider
     */
    public $categoryTreeChoiceProvider;

    /**
     * CategoryTreeIterator constructor.
     *
     * @param CategoryTreeChoiceProvider $categoryTreeChoiceProvider
     */
    public function __construct(CategoryTreeChoiceProvider $categoryTreeChoiceProvider)
    {
        $this->categoryTreeChoiceProvider = $categoryTreeChoiceProvider;
    }

    public function getCategoryId(string $categoryName): ?int
    {
        $categoryTreeChoicesArray = $this->categoryTreeChoiceProvider->getChoices();

        return $this->getCategoryNodeId($categoryName, $categoryTreeChoicesArray);
    }

    /**
     * @param string $categoryName
     * @param array $nodes
     *
     * @return int|void|null
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
