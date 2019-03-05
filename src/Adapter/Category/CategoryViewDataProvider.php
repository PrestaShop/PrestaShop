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

namespace PrestaShop\PrestaShop\Adapter\Category;

use Category;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Tools;

/**
 * Class CategoryViewDataProvider provides category view data for categories listing page.
 *
 * @internal
 */
class CategoryViewDataProvider
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FeatureInterface
     */
    private $multishopFeature;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var Context
     */
    private $shopContext;

    /**
     * @param ConfigurationInterface $configuration
     * @param FeatureInterface $multishopFeature
     * @param Context $shopContext
     * @param int $contextLangId
     */
    public function __construct(
        ConfigurationInterface $configuration,
        FeatureInterface $multishopFeature,
        Context $shopContext,
        $contextLangId
    ) {
        $this->configuration = $configuration;
        $this->multishopFeature = $multishopFeature;
        $this->contextLangId = $contextLangId;
        $this->shopContext = $shopContext;
    }

    /**
     * Get category view data.
     *
     * @param int $categoryId
     *
     * @return array
     */
    public function getViewData($categoryId)
    {
        $category = new Category($categoryId);

        $categoriesWithoutParentCount = count(Category::getCategoriesWithoutParent());
        $categoriesTree = $category->getParentsCategories();

        if (empty($categoriesTree)
            && ($category->id != (int) $this->configuration->get('PS_ROOT_CATEGORY') || Tools::isSubmit('id_category'))
            && $this->shopContext->isShopContext()
            && !$this->multishopFeature->isUsed()
            && $categoriesWithoutParentCount > 1
        ) {
            $categoriesTree = [['name' => $category->name[$this->contextLangId]]];
        }

        $categoriesTree = array_reverse($categoriesTree);

        return [
            'breadcrumb_tree' => $categoriesTree,
            'id' => $category->id,
            'id_parent' => $category->id_parent,
            'is_home_category' => $this->configuration->get('PS_HOME_CATEGORY') == $category->id,
            'name' => $category->name[$this->contextLangId],
        ];
    }
}
