<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @param CategoryTreeChoiceProvider$categoryTreeChoiceProvider
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
