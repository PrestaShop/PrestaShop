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

namespace PrestaShopBundle\Translation\Api;

class InternationalApi extends AbstractApi
{
    /**
     * @return string[] List of translations
     */
    public function getTranslations()
    {
        return [
            'button_reset' => $this->translator->trans('Reset', [], 'Admin.Actions'),
            'button_save' => $this->translator->trans('Save', [], 'Admin.Global'),
            'button_leave' => $this->translator->trans('Leave anyway', [], 'Admin.Notifications.Warning'),
            'button_search' => $this->translator->trans('Search', [], 'Admin.Actions'),
            'modal_title' => $this->translator->trans('Confirm this action', [], 'Admin.Actions'),
            'modal_content' => $this->translator->trans('Your modifications are not saved yet. Do you wish to save it before leaving?', [], 'Admin.Notifications.Warning'),
            'head_title' => $this->translator->trans('Translations', [], 'Admin.Navigation.Menu'),
            /* Missing is plural, always > 1 */
            'label_missing' => $this->translator->trans('%d missing', [], 'Admin.International.Feature'),
            'label_missing_singular' => $this->translator->trans('1 missing', [], 'Admin.International.Feature'),
            'label_total_domain' => $this->translator->trans('%nb_translations% expressions', [], 'Admin.International.Feature'),
            /* nb_translations can be 0 or 1 */
            'label_total_domain_singular' => $this->translator->trans('%nb_translation% expression', [], 'Admin.International.Feature'),
            'link_international' => $this->translator->trans('International', [], 'Admin.Navigation.Menu'),
            'link_translations' => $this->translator->trans('Translations', [], 'Admin.Navigation.Menu'),
            'no_result' => $this->translator->trans('There are no results matching your query "%s".', [], 'Admin.Navigation.Search'),
            'sidebar_expand' => $this->translator->trans('Expand', [], 'Admin.Actions'),
            'sidebar_collapse' => $this->translator->trans('Collapse', [], 'Admin.Actions'),
            'search_info' => $this->translator->trans('%d results match your query "%s".', [], 'Admin.Navigation.Search'),
            /* %d can be 0 or 1 */
            'search_info_singular' => $this->translator->trans('%d result matches your query "%s".', [], 'Admin.Navigation.Search'),
            'search_label' => $this->translator->trans('Search translations', [], 'Admin.International.Feature'),
            'search_placeholder' => $this->translator->trans('Search a word or expression, e.g.: "Order confirmation"', [], 'Admin.International.Help'),
        ];
    }
}
