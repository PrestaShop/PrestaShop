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

namespace PrestaShop\PrestaShop\Adapter\Feature;

use Feature;
use FeatureValue;

/**
 * This class will provide data from DB / ORM about Feature.
 */
class FeatureDataProvider
{
    /**
     * Get all features for a given language.
     *
     * @param int $id_lang Language id
     * @param bool $with_shop
     *
     * @return array Multiple arrays with feature's data
     */
    public static function getFeatures($id_lang, $with_shop = true)
    {
        return Feature::getFeatures($id_lang, $with_shop);
    }

    /**
     * Get all values for a given feature and language.
     *
     * @param int $id_lang Language id
     * @param int $id_feature Feature id
     * @param bool $custom
     *
     * @return array Array with feature's values
     */
    public static function getFeatureValuesWithLang($id_lang, $id_feature, $custom = false)
    {
        return FeatureValue::getFeatureValuesWithLang($id_lang, $id_feature, $custom);
    }

    /**
     * Get all language for a given value.
     *
     * @param int $id_feature_value Feature value id
     *
     * @return array Array with value's languages
     */
    public static function getFeatureValueLang($id_feature_value)
    {
        return FeatureValue::getFeatureValueLang($id_feature_value);
    }
}
