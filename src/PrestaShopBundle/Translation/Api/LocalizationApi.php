<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Api;

class LocalizationApi extends AbstractApi
{
    /**
     * @return string[] List of translations
     */
    public function getTranslations()
    {
        return array(
            'button_cancel' => $this->translator->trans('Cancel', array(), 'Admin.Global'),
            'button_save' => $this->translator->trans('Save', array(), 'Admin.Global'),
            'label_code' => $this->translator->trans('ISO code', array(), 'Admin.International'),
            'label_code_numeric' => $this->translator->trans('Numeric ISO code', array(), 'Admin.International'),
            'label_code_language' => $this->translator->trans('Language code', array(), 'Admin.International'),
            'label_currency' => $this->translator->trans('Choose a currency', array(), 'Admin.International'),
            'label_currency_format' => $this->translator->trans('Currency format', array(), 'Admin.International'),
            'label_currency_name' => $this->translator->trans('Currency name', array(), 'Admin.International'),
            'label_date_format' => $this->translator->trans('Date format', array(), 'Admin.International'),
            'label_date_format_full' => $this->translator->trans('Date format (full)', array(), 'Admin.International'),
            'label_decimals' => $this->translator->trans('Decimals', array(), 'Admin.International'),
            'label_exchange' => $this->translator->trans('Exchange rate', array(), 'Admin.International'),
            'label_exchange' => $this->translator->trans('Exchange rate', array(), 'Admin.International'),
            'label_file' => $this->translator->trans('Add File', array(), 'Admin.International'),
            'label_flag' => $this->translator->trans('Flag', array(), 'Admin.International'),
            'label_format' => $this->translator->trans('Format', array(), 'Admin.International'),
            'label_format_preview' => $this->translator->trans('Format preview', array(), 'Admin.International'),
            'label_language' => $this->translator->trans('Choose a language', array(), 'Admin.International'),
            'label_language_name' => $this->translator->trans('Language name', array(), 'Admin.International'),
            'label_image' => $this->translator->trans('"No-picture" image', array(), 'Admin.International'),
            'label_reset' => $this->translator->trans('Reset settings', array(), 'Admin.International'),
            'label_switch' => $this->translator->trans('Enabled', array(), 'Admin.International'),
            'label_switch_rtl' => $this->translator->trans('Right to Left', array(), 'Admin.International'),
            'label_symbol' => $this->translator->trans('Symbol', array(), 'Admin.International'),
            'table_title_language' => $this->translator->trans('Language', array(), 'Admin.International'),
            'table_title_edit' => $this->translator->trans('Example', array(), 'Admin.International'),
            'table_title_example' => $this->translator->trans('Edit', array(), 'Admin.International'),
            'title_currency' => $this->translator->trans('Add new currency', array(), 'Admin.International'),
            'title_format' => $this->translator->trans('Format preview', array(), 'Admin.International'),
            'title_languages' => $this->translator->trans('Language format', array(), 'Admin.International'),
        );
    }
}
