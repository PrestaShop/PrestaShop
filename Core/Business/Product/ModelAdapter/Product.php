<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Business\Product\ModelApdapter;

use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;

/**
 * This form class is risponsible to mapp the form data to the product object
 */
class Product
{
    //define translatable key
    private static $translatable_keys = array('name', 'description', 'description_short', 'link_rewrite');

    //define unused key for manual binding
    private static $unmap_keys = array('name', 'description', 'description_short', 'images', 'related_products', 'categories', 'suppliers', 'options');

    /**
     * modelMapper
     * Mapp form data to object model
     *
     * @param array $from_data
     * @param Container $container
     * @param array $locales
     *
     * @return array Transormed form data to model attempt
     */
    public static function modelMapper($form_data, Container $container, $locales = array())
    {
        $context = $container->make('Context');

        //merge all form steps
        $form_data = array_merge(['id_product' => $form_data['id_product']], $form_data['step1'], $form_data['step2'], $form_data['step3'], $form_data['step4'], $form_data['step5']);

        //extract description_short from description
        foreach ($locales as $locale) {
            if ($form_data['description'][$locale['id_lang']] && false !== strpos($form_data['description'][$locale['id_lang']], '<p><!-- excerpt --></p>')) {
                $description_full = explode('<p><!-- excerpt --></p>', $form_data['description'][$locale['id_lang']]);
                $form_data['description'][$locale['id_lang']] = isset($description_full[1]) ? $description_full[1] : $description_full[0];
                $form_data['description_short'][$locale['id_lang']] = isset($description_full[1]) ? $description_full[0] : '';
            } else {
                $form_data['description_short'][$locale['id_lang']] = '';
            }
        }

        //map translatable
        foreach (self::$translatable_keys as $field) {
            foreach ($form_data[$field] as $lang_id => $translate_value) {
                $form_data[$field.'_'.$lang_id] = $translate_value;
            }
        }

        //map categories
        foreach ($form_data['categories']['tree'] as $category) {
            $form_data['categoryBox'][] = $category;
        }

        //if empty categories, set default one
        if (empty($form_data['categoryBox'])) {
            $form_data['categoryBox'][] = $context->shop->id_category;
        }

        //if default category not define, set the default one
        if (empty($form_data['id_category_default'])) {
            $form_data['id_category_default'] = $context->shop->id_category;
        }

        //map suppliers
        if (!empty($form_data['suppliers'])) {
            foreach ($form_data['suppliers'] as $id_supplier) {
                $form_data['check_supplier_'.$id_supplier] = 1;
            }
        }
        $form_data['supplier_loaded'] = 1;

        //map options
        foreach ($form_data['options'] as $option => $value) {
            $from_data[$option] = $value;
        }

        //if empty, set link_rewrite for default locale
        if (empty($form_data['link_rewrite_'.$locales[0]['id_lang']])) {
            $form_data['link_rewrite_'.$locales[0]['id_lang']] = \Tools::link_rewrite($form_data['name_'.$locales[0]['id_lang']]);
        }

        //map inputAccessories
        if (!empty($form_data['related_products']) && !empty($form_data['related_products']['data'])) {
            $inputAccessories = '';
            foreach ($form_data['related_products']['data'] as $accessoryIds) {
                $accessoryIds = explode(',', $accessoryIds);
                $inputAccessories .= $accessoryIds[0].'-';
            }
            $form_data['inputAccessories'] = $inputAccessories;
        }

        //map all
        $new_form_data = [];
        foreach ($form_data as $k => $v) {
            if (in_array($k, self::$unmap_keys) || in_array($k, self::$translatable_keys)) {
                continue;
            }
            $new_form_data[$k] = $v;
        }

        return $new_form_data;
    }

    /**
     * formMapper
     * Mapp object model to form data
     *
     * @param object $model_data
     * @param Container $container
     * @param array $locales
     *
     * @return array Transormed model datas to form attempt
     */
    public static function formMapper($model_data, Container $container, $locales = array())
    {
        $context = $container->make('Context');
        $productAdapter = $container->make('CoreAdapter:Product\\ProductDataProvider');

        $default_data = [
            'id_product' => 0,
            'step1' => [
                'type_product' => 0,
                'condition' => 'new',
                'id_tax_rules_group' => $productAdapter->getIdTaxRulesGroup(),
                'price' => 0,
                'active' => 0,
                'options' => [
                    'available_for_order' => true,
                    'show_price' => true,
                ],
                'categories' => ['tree' => [$context->shop->id_category]]
            ],
            'step5' => [
                'visibility' => 'both'
            ]
        ];

        //if empty model_data, return the default value object
        if (!$model_data) {
            return $default_data;
        }

        //echo $model_data->price;die;
        $form_data = [
            'id_product' => $model_data->id,
            'step1' => [
                'type_product' => $model_data->getType(),
                'name' => $model_data->name,
                'description' => self::getFormFullDescription($model_data->description, $model_data->description_short, $locales),
                //images
                'upc' => $model_data->upc,
                'ean13' => $model_data->ean13,
                'isbn' => $model_data->isbn,
                'reference' => $model_data->reference,
                'condition' => $model_data->condition,
                'price' => $model_data->price,
                'id_tax_rules_group' => $model_data->id_tax_rules_group,
                'on_sale' => (bool) $model_data->on_sale,
                'active' => $model_data->active,
                'options' => [
                    'available_for_order' => (bool) $model_data->available_for_order,
                    'show_price' => (bool) $model_data->show_price,
                    'online_only' => (bool) $model_data->online_only,
                ],
                'categories' => ['tree' => $model_data->getCategories()],
                'id_manufacturer' => $model_data->id_manufacturer,
                'related_products' => [
                    'data' => array_map(
                        function ($p) {
                            return($p['id_product']);
                        },
                        $model_data::getAccessoriesLight($locales[0]['id_lang'], $model_data->id)
                    )
                ]
            ],
            'step4' => [
                'link_rewrite' => $model_data->link_rewrite,
            ],
            'step5' => [
                'visibility' => $model_data->visibility,
                'wholesale_price' => $model_data->wholesale_price,
                'unit_price' => $model_data->unit_price_ratio != 0  ? $model_data->price / $model_data->unit_price_ratio : 0,
                'unity' => $model_data->unity,
                'suppliers' => array_map(
                    function ($s) {
                        return($s->id_supplier);
                    },
                    $container->make('CoreAdapter:Supplier\\SupplierDataProvider')->getProductSuppliers($model_data->id)
                ),
                'default_supplier' => $model_data->id_supplier
            ]
        ];

        return $form_data;
    }

    /**
     * get form Full product Description with description short
     * Mapp object model to form data
     *
     * @param array $descriptionLangs the translated descriptions
     * @param array $descriptionShortLangs the translated short descriptions
     * @param array $locales
     *
     * @return array full translated description with excerpt tag
     */
    private static function getFormFullDescription(array $descriptionLangs, array $descriptionShortLangs, $locales)
    {
        $full_descriptions = [];

        foreach ($locales as $locale) {
            if (strlen($descriptionShortLangs[$locale['id_lang']]) > 0) {
                $full_descriptions[$locale['id_lang']] =
                    $descriptionShortLangs[$locale['id_lang']].
                    '<p><!-- excerpt --></p>'.
                    $descriptionLangs[$locale['id_lang']];
            } else {
                $full_descriptions[$locale['id_lang']] = $descriptionLangs[$locale['id_lang']];
            }
        }

        return $full_descriptions;
    }
}
