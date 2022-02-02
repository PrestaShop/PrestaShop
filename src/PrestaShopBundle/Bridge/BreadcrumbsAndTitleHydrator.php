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

namespace PrestaShopBundle\Bridge;

use \Configuration;
use \Tab;
use \Tools;

/**
 * Class BreadcrumbsAndTitleHydrator hydrate breadcrumbs in ControllerConfiguration
 *
 * Only set variable
 */
class BreadcrumbsAndTitleHydrator implements HydratorInterface
{
    /**
     * Set breadcrumbs array for the controller page.
     *
     * @param ControllerConfiguration $controllerConfiguration
     */
    public function hydrate(ControllerConfiguration $controllerConfiguration)
    {
        $tabs = [];

        $tabId = $controllerConfiguration->id;
        $tabs = Tab::recursiveTab($tabId, $tabs);

        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs2 = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];

        if (!empty($tabs[0])) {
            $this->addMetaTitle($controllerConfiguration, $tabs[0]['name']);
            $breadcrumbs2['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs2['tab']['href'] = $controllerConfiguration->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs2['container']['name'] = $tabs[1]['name'];
            $breadcrumbs2['container']['href'] = $controllerConfiguration->link->getTabLink($tabs[1]);
            $breadcrumbs2['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        /* content, edit, list, add, details, options, view */
        //switch ($this->display) {
        //    case 'add':
        //        $breadcrumbs2['action']['name'] = $this->trans('Add', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-plus';
        //
        //        break;
        //    case 'edit':
        //        $breadcrumbs2['action']['name'] = $this->trans('Edit', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-pencil';
        //
        //        break;
        //    case '':
        //    case 'list':
        //        $breadcrumbs2['action']['name'] = $this->trans('List', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-th-list';
        //
        //        break;
        //    case 'details':
        //    case 'view':
        //        $breadcrumbs2['action']['name'] = $this->trans('View details', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-zoom-in';
        //
        //        break;
        //    case 'options':
        //        $breadcrumbs2['action']['name'] = $this->trans('Options', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-cogs';
        //
        //        break;
        //    case 'generator':
        //        $breadcrumbs2['action']['name'] = $this->trans('Generator', 'Admin.Actions');
        //        $breadcrumbs2['action']['icon'] = 'icon-flask';
        //
        //        break;
        //}
        $controllerConfiguration->breadcrumbs[] = $tabs[0]['name'] ?? '';

        $controllerConfiguration->templatesVars['breadcrumbs2'] = $breadcrumbs2;
        $controllerConfiguration->templatesVars['quick_access_current_link_name'] = Tools::safeOutput($breadcrumbs2['tab']['name'] . (isset($breadcrumbs2['action']) ? ' - ' . $breadcrumbs2['action']['name'] : ''));
        $controllerConfiguration->templatesVars['quick_access_current_link_icon'] = $breadcrumbs2['container']['icon'];

        $navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
        $controllerConfiguration->templatesVars['navigationPipe'] = $navigationPipe;
    }

    /**
     * Add an entry to the meta title.
     *
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $entry new entry
     *
     * @Todo moved it
     */
    public function addMetaTitle(ControllerConfiguration $controllerConfiguration, $entry)
    {
        if (is_array($controllerConfiguration->metaTitle)) {
            $controllerConfiguration->metaTitle[] = $entry;
        }
    }
}
