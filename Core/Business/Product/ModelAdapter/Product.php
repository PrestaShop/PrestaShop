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
    private static $translatable_keys = array('name', 'description', 'link_rewrite');

    //define unused key for manual binding
    private static $unmap_keys = array('name', 'description', 'images', 'related_products', 'categories', 'suppliers', 'options');

    /**
     * modelMapper
     * Mapp form data to object model
     *
     * @param array $fom_data
     * @param Container $container
     * @param array $locales
     *
     * @return array Transormed form data to model attempt
     */
    public static function modelMapper($fom_data, Container $container, $locales = array())
    {
        $context = $container->make('Context');

        //merge all form steps
        $fom_data = array_merge(['id' => $fom_data['id']], $fom_data['step1'], $fom_data['step2'], $fom_data['step3'], $fom_data['step4'], $fom_data['step5']);

        //map translatable
        foreach (self::$translatable_keys as $field) {
            foreach ($fom_data[$field] as $lang_id => $translate_value) {
                $fom_data[$field.'_'.$lang_id] = $translate_value;
            }
        }

        //map categories
        foreach ($fom_data['categories']['tree'] as $category) {
            $fom_data['categoryBox'][] = $category;
        }

        //if empty categories, set default one
        if (empty($fom_data['categoryBox'])) {
            $fom_data['categoryBox'][] = $context->shop->id_category;
        }

        //if default category not define, set the default one
        if (empty($fom_data['id_category_default'])) {
            $fom_data['id_category_default'] = $context->shop->id_category;
        }

        //map options
        foreach ($fom_data['options'] as $option => $value) {
            $fom_data[$option] = $value;
        }

        //if empty, set link_rewrite for default locale
        if (empty($fom_data['link_rewrite_'.$locales[0]['id_lang']])) {
            $fom_data['link_rewrite_'.$locales[0]['id_lang']] = \Tools::link_rewrite($fom_data['name_'.$locales[0]['id_lang']]);
        }

        //map all
        $new_form_data = [];
        foreach ($fom_data as $k => $v) {
            if (in_array($k, self::$unmap_keys) || in_array($k, self::$translatable_keys)) {
                continue;
            }
            $new_form_data[$k] = $v;
        }

        //print_r($new_form_data);die;
        return $new_form_data;
    }

    /**
     * formMapper
     * Mapp object model to form data
     *
     * @param array $model_data
     *
     * @return array Transormed model datas to form attempt
     */
    public static function formMapper($model_data)
    {
        //TODO
        return $model_data;
    }
}
