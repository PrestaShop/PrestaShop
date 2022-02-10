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

namespace PrestaShopBundle\Bridge\Smarty;

use \Configuration;
use \Link;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use \Tab;
use \Tools;

/**
 * Class BreadcrumbsAndTitleHydrator hydrate breadcrumbs in ControllerConfiguration
 */
class BreadcrumbsAndTitleHydrator implements HydratorInterface
{
    /**
     * @var Link
     */
    private $link;

    public function __construct(LegacyContext $legacyContext)
    {
        $this->link = $legacyContext->getContext()->link;
    }

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
            $breadcrumbs2['tab']['href'] = $this->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs2['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs2['container']['name'] = $tabs[1]['name'];
            $breadcrumbs2['container']['href'] = $this->link->getTabLink($tabs[1]);
            $breadcrumbs2['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

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
     */
    public function addMetaTitle(ControllerConfiguration $controllerConfiguration, $entry)
    {
        if (is_array($controllerConfiguration->metaTitle)) {
            $controllerConfiguration->metaTitle[] = $entry;
        }
    }
}
