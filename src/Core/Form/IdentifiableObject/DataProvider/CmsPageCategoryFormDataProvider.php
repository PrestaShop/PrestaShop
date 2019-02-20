<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.01
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;


final class CmsPageCategoryFormDataProvider implements FormDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        return [
            'name' => [],
            'is_displayed' => false,
            'parent_category' => 0,
            'description' => [],
            'meta_title' => [],
            'meta_description' => [],
            'meta_keywords' => [],
            'friendly_url' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return null;
    }
}
