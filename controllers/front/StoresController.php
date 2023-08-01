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
use PrestaShop\PrestaShop\Adapter\Presenter\Store\StorePresenter;

class StoresControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'stores';

    /** @var StorePresenter */
    protected $storePresenter;

    /**
     * Initialize stores controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        // Initialize presenter, we will use it for all cases
        $this->storePresenter = new StorePresenter(
            $this->context->link,
            $this->context->getTranslator()
        );

        parent::init();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $distance_unit = Configuration::get('PS_DISTANCE_UNIT');
        if (!in_array($distance_unit, ['km', 'mi'])) {
            $distance_unit = 'km';
        }

        // Load stores and present them
        $stores = $this->getTemplateVarStores();

        // If no stores are configured, we hide this page
        if (!empty($stores)) {
            $this->context->smarty->assign([
                'mediumSize' => Image::getSize(ImageType::getFormattedName('medium')),
                'searchUrl' => $this->context->link->getPageLink('stores'),
                'distance_unit' => $distance_unit,
                'stores' => $stores,
            ]);
            parent::initContent();
            $this->setTemplate('cms/stores');
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    public function getTemplateVarStores()
    {
        $stores = Store::getStores($this->context->language->id);

        foreach ($stores as &$store) {
            $store = $this->storePresenter->present(
                $store,
                $this->context->language
            );
        }

        return $stores;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Our stores', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('stores', true),
        ];

        return $breadcrumb;
    }
}
