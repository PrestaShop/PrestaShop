<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Api;

class InternationalApi extends AbstractApi
{
    /**
     * @return string[] List of translations
     */
    public function getTranslations()
    {
        return array(
            'button_save' => $this->translator->trans('Save', array(), 'Admin.Global'),
            'button_search' => $this->translator->trans('Search', array(), 'Admin.Actions'),
            'head_title' => $this->translator->trans('Translations', array(), 'Admin.Navigation.Menu'),
            'label_missing' => $this->translator->trans('%d missing', array(), 'Admin.International.Feature'),
            'sidebar_expand' => $this->translator->trans('Expand', array(), 'Admin.Actions'),
            'sidebar_collapse' => $this->translator->trans('Collapse', array(), 'Admin.Actions'),
            'search_label' => $this->translator->trans('Search translations', array(), 'Admin.International.Feature'),
            'search_placeholder' => $this->translator->trans('Search a word or expression, e.g.: "Order confirmation"', array(), 'Admin.International.Help'),
        );
    }
}
