<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $categoryName = $category->name[$this->contextLangId] ?? null;

        if (empty($categoriesTree)
            && ($category->id != (int) $this->configuration->get('PS_ROOT_CATEGORY') || Tools::isSubmit('id_category'))
            && $this->shopContext->isShopContext()
            && !$this->multishopFeature->isUsed()
            && $categoriesWithoutParentCount > 1
        ) {
            $categoriesTree = [['name' => $categoryName]];
        }

        $categoriesTree = array_reverse($categoriesTree);

        return [
            'breadcrumb_tree' => $categoriesTree,
            'id' => $category->id,
            'id_parent' => $category->id_parent,
            'is_home_category' => $this->configuration->get('PS_HOME_CATEGORY') == $category->id,
            'name' => $categoryName,
        ];
    }
}
