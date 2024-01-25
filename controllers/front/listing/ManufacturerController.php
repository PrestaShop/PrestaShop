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
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class ManufacturerControllerCore extends ProductListingFrontController
{
    /** @var string */
    public $php_self = 'manufacturer';

    /** @var Manufacturer|null */
    protected $manufacturer;
    protected $label;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    public function getCanonicalURL(): string
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            return $this->buildPaginatedUrl($this->context->link->getManufacturerLink($this->manufacturer));
        }

        return $this->context->link->getPageLink('manufacturer');
    }

    /**
     * Initialize manufaturer controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if ($id_manufacturer = Tools::getValue('id_manufacturer')) {
            $this->manufacturer = new Manufacturer((int) $id_manufacturer, $this->context->language->id);

            if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop()) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->canonicalRedirection();
            }
        }

        parent::init();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::get('PS_DISPLAY_MANUFACTURERS')) {
            parent::initContent();

            if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop()) {
                $this->assignManufacturer();
                $this->label = $this->trans(
                    'List of products by brand %brand_name%',
                    [
                        '%brand_name%' => $this->manufacturer->name,
                    ],
                    'Shop.Theme.Catalog'
                );
                $this->doProductSearch(
                    'catalog/listing/manufacturer',
                    ['entity' => 'manufacturer', 'id' => $this->manufacturer->id]
                );
            } else {
                $this->assignAll();
                $this->label = $this->trans(
                    'List of all brands',
                    [],
                    'Shop.Theme.Catalog'
                );
                $this->setTemplate('catalog/manufacturers', ['entity' => 'manufacturers']);
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * @return ProductSearchQuery
     *
     * @throws \PrestaShop\PrestaShop\Core\Product\Search\Exception\InvalidSortOrderDirectionException
     */
    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('manufacturer')
            ->setIdManufacturer($this->manufacturer->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));

        return $query;
    }

    /**
     * @return ManufacturerProductSearchProvider
     */
    protected function getDefaultProductSearchProvider()
    {
        return new ManufacturerProductSearchProvider(
            $this->getTranslator(),
            $this->manufacturer
        );
    }

    /**
     * Assign template vars if displaying one manufacturer.
     */
    protected function assignManufacturer()
    {
        $manufacturerVar = $this->objectPresenter->present($this->manufacturer);

        // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
        $filteredManufacturer = Hook::exec(
            'filterManufacturerContent',
            ['filtered_content' => $manufacturerVar['description']],
            null,
            false,
            true,
            false,
            null,
            true
        );
        if (!empty($filteredManufacturer)) {
            $manufacturerVar['description'] = $filteredManufacturer;
        }

        $this->context->smarty->assign([
            'manufacturer' => $manufacturerVar,
        ]);
    }

    /**
     * Assign template vars if displaying the manufacturer list.
     */
    protected function assignAll()
    {
        $manufacturersVar = $this->getTemplateVarManufacturers();

        if (!empty($manufacturersVar)) {
            foreach ($manufacturersVar as $k => $manufacturer) {
                // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
                $filteredManufacturer = Hook::exec(
                    'filterManufacturerContent',
                    ['filtered_content' => $manufacturer['text']],
                    null,
                    false,
                    true,
                    false,
                    null,
                    true
                );
                if (!empty($filteredManufacturer)) {
                    $manufacturersVar[$k]['text'] = $filteredManufacturer;
                }
            }
        }

        $this->context->smarty->assign([
            'brands' => $manufacturersVar,
        ]);
    }

    public function getTemplateVarManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(true, $this->context->language->id);
        $manufacturers_for_display = [];

        foreach ($manufacturers as $manufacturer) {
            $manufacturers_for_display[$manufacturer['id_manufacturer']] = $manufacturer;
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['text'] = $manufacturer['short_description'];
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['image'] = $this->context->link->getManufacturerImageLink($manufacturer['id_manufacturer'], 'small_default');
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['url'] = $this->context->link->getManufacturerLink($manufacturer['id_manufacturer']);
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['nb_products'] = $manufacturer['nb_products'] > 1 ? ($this->trans('%number% products', ['%number%' => $manufacturer['nb_products']], 'Shop.Theme.Catalog')) : $this->trans('%number% product', ['%number%' => $manufacturer['nb_products']], 'Shop.Theme.Catalog');
        }

        return $manufacturers_for_display;
    }

    public function getListingLabel()
    {
        return $this->label;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->getTranslator()->trans('Brands', [], 'Shop.Theme.Global'),
            'url' => $this->context->link->getPageLink('manufacturer'),
        ];

        if (!empty($this->manufacturer)) {
            $breadcrumb['links'][] = [
                'title' => $this->manufacturer->name,
                'url' => $this->context->link->getManufacturerLink($this->manufacturer),
            ];
        }

        return $breadcrumb;
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if (!empty($this->manufacturer)) {
            $page['body_classes']['manufacturer-id-' . $this->manufacturer->id] = true;
            $page['body_classes']['manufacturer-' . $this->manufacturer->name] = true;
        }

        return $page;
    }

    /**
     * @return Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
}
