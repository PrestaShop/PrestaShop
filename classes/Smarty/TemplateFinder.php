<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Determine the best existing template.
 */
class TemplateFinderCore
{
    private $directories;
    private $extension;
    private $productListEntities = ['category', 'manufacturer', 'supplier'];
    private $productListSearchEntities = ['search', 'price-drop', 'best-sale', 'prices-drop', 'best-sales', 'new-products'];
    private $productEntities = ['product'];
    private $brandListEntities = ['manufacturers', 'suppliers'];

    public function __construct(array $directories, $extension)
    {
        $this->directories = $directories;
        $this->extension = $extension;
    }

    public function getTemplate($template, $entity, $id, $locale)
    {
        $locale = (Validate::isLocale($locale)) ? $locale : '';

        $templates = $this->getTemplateHierarchy($template, $entity, $id);

        foreach ($this->directories as $dir) {
            foreach ($templates as $tpl) {
                if (!empty($locale) && is_file($dir . $locale . DIRECTORY_SEPARATOR . $tpl . $this->extension)) {
                    return $locale . DIRECTORY_SEPARATOR . $tpl . $this->extension;
                }
                if (is_file($dir . $tpl . $this->extension)) {
                    return $tpl . $this->extension;
                }
                if (is_file($dir . $tpl) && false !== strpos($tpl, $this->extension)) {
                    return $tpl;
                }
            }
        }

        throw new PrestaShopException('No template found for ' . $template);
    }

    private function getTemplateHierarchy($template, $entity, $id)
    {
        $entity = basename($entity ?? '');
        $id = (int) $id;

        if (in_array($entity, $this->getProductListEntities())) {
            $templates = [
                'catalog/listing/' . $entity . '-' . $id,
                'catalog/listing/' . $entity,
                $template,
                'catalog/listing/product-list',
            ];
        } elseif (in_array($entity, $this->getProductListSearchEntities())) {
            $templates = [
                'catalog/listing/' . $entity,
                $template,
                'catalog/listing/product-list',
            ];
        } elseif (in_array($entity, $this->getProductEntities())) {
            $templates = [
                'catalog/' . $entity . '-' . $id,
                $template,
                'catalog/product',
            ];
        } elseif (in_array($entity, $this->getBrandListEntities())) {
            $templates = [
                $template,
                'catalog/brands',
            ];
        } elseif ('cms' === $entity) {
            $templates = [
                'cms/page-' . $id,
                $template,
                'cms/page',
            ];
        } elseif ('cms_category' === $entity) {
            $templates = [
                'cms/category-' . $id,
                $template,
                'cms/category',
            ];
        } else {
            $templates = [$template];
        }

        return array_unique($templates);
    }

    /**
     * Get productListEntities.
     *
     * @return array
     */
    public function getProductListEntities()
    {
        return $this->productListEntities;
    }

    /**
     * Set productListEntities.
     *
     * @param array $productListEntities
     *
     * @return TemplateFinderCore
     */
    public function setProductListEntities($productListEntities)
    {
        $this->productListEntities = $productListEntities;

        return $this;
    }

    /**
     * Get productListSearch.
     *
     * @return array
     */
    public function getProductListSearchEntities()
    {
        return $this->productListSearchEntities;
    }

    /**
     * Set productListSearch.
     *
     * @param array $productListSearchEntities
     *
     * @return TemplateFinderCore
     */
    public function setProductListSearchEntities($productListSearchEntities)
    {
        $this->productListSearchEntities = $productListSearchEntities;

        return $this;
    }

    /**
     * Get productEntities.
     *
     * @return array
     */
    public function getProductEntities()
    {
        return $this->productEntities;
    }

    /**
     * Set productEntities.
     *
     * @param array $productEntities
     *
     * @return TemplateFinderCore
     */
    public function setProductEntities($productEntities)
    {
        $this->productEntities = $productEntities;

        return $this;
    }

    /**
     * Get brandListEntities.
     *
     * @return array
     */
    public function getBrandListEntities()
    {
        return $this->brandListEntities;
    }

    /**
     * Set brandListEntities.
     *
     * @param array $brandListEntities
     *
     * @return TemplateFinderCore
     */
    public function setBrandListEntities($brandListEntities)
    {
        $this->brandListEntities = $brandListEntities;

        return $this;
    }
}
