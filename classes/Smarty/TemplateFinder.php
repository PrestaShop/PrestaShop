<?php

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Determine the best existing template.
 *
 * @since 1.7.0.0
 */
class TemplateFinderCore
{
    private $directories;
    private $extension;
    private $productListEntities = array('category', 'manufacturer', 'supplier');
    private $productListSearch = array('search', 'price-drop', 'best-sale');

    public function __construct(array $directories, $extension)
    {
        $this->directories = $directories;
        $this->extension = $extension;
    }

    public function getTemplate($template, $entity, $id)
    {
        $templates = $this->getTemplateHierarchy($template, $entity, $id);

        foreach ($this->directories as $dir) {
            foreach ($templates as $tpl) {
                if (file_exists($dir.$tpl.$this->extension)) {
                    return $tpl.$this->extension;
                }
            }
        }

        throw new PrestaShopException('No template found for '.$template);
    }

    private function getTemplateHierarchy($template, $entity, $id)
    {
        $entity = basename($entity);
        $id = (int) $id;

        if (in_array($entity, $this->getProductListEntities())) {
            $templates = array(
                'catalog/listing/'.$entity.'-'.$id,
                'catalog/listing/'.$entity,
                $template,
                'catalog/listing/product-list',
            );
        } elseif (in_array($entity, $this->getProductListSearch())) {
            $templates = array(
                'catalog/listing/'.$entity,
                $template,
                'catalog/listing/product-list',
            );
        } else {
            $templates = array($template);
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
    public function getProductListSearch()
    {
        return $this->productListSearch;
    }

    /**
     * Set productListSearch.
     *
     * @param array $productListSearch
     *
     * @return TemplateFinderCore
     */
    public function setProductListSearch($productListSearch)
    {
        $this->productListSearch = $productListSearch;

        return $this;
    }
}
