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
