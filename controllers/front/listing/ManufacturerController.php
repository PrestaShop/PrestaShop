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
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;

class ManufacturerControllerCore extends ProductListingFrontController
{
    public $php_self = 'manufacturer';

    protected $manufacturer;
    private $label;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
        } elseif ($canonicalURL) {
            parent::canonicalRedirection($canonicalURL);
        }
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
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop()) {
                $this->assignManufacturer();
                $this->label = $this->trans(
                    'List of products by brand %brand_name%', array(
                        '%brand_name%' => $this->manufacturer->name
                        ),
                    'Shop.Theme.Catalog'
                );
                $this->doProductSearch(
                    'catalog/listing/manufacturer',
                    array('entity' => 'manufacturer', 'id' => $this->manufacturer->id)
                );
            } else {
                $this->assignAll();
                $this->label = $this->trans(
                    'List of all brands', array(), 'Shop.Theme.Catalog'
                );
                $this->setTemplate('catalog/manufacturers', array('entity' => 'manufacturers'));
            }
            parent::initContent();
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdManufacturer($this->manufacturer->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')));
        ;

        return $query;
    }

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

        $filteredManufacturer = Hook::exec(
            'filterManufacturerContent',
            array('filtered_content' => $manufacturerVar['description']),
            $id_module = null,
            $array_return = false,
            $check_exceptions = true,
            $use_push = false,
            $id_shop = null,
            $chain = true
        );
        if (!empty($filteredManufacturer)) {
            $manufacturerVar['description'] = $filteredManufacturer;
        }

        $this->context->smarty->assign(array(
            'manufacturer' => $manufacturerVar,
        ));
    }

    /**
     * Assign template vars if displaying the manufacturer list.
     */
    protected function assignAll()
    {
        $manufacturersVar = $this->getTemplateVarManufacturers();

        if (!empty($manufacturersVar)) {
            foreach ($manufacturersVar as $k => $manufacturer) {
                $filteredManufacturer = Hook::exec(
                    'filterManufacturerContent',
                    array('filtered_content' => $manufacturer['text']),
                    $id_module = null,
                    $array_return = false,
                    $check_exceptions = true,
                    $use_push = false,
                    $id_shop = null,
                    $chain = true
                );
                if (!empty($filteredManufacturer)) {
                    $manufacturersVar[$k]['text'] = $filteredManufacturer;
                }
            }
        }

        $this->context->smarty->assign(array(
            'brands' => $manufacturersVar,
        ));
    }

    public function getTemplateVarManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(true, $this->context->language->id, true, $this->p, $this->n, false);
        $manufacturers_for_display = array();

        foreach ($manufacturers as $manufacturer) {
            $manufacturers_for_display[$manufacturer['id_manufacturer']] = $manufacturer;
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['text'] = $manufacturer['short_description'];
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['image'] = $this->context->link->getManufacturerImageLink($manufacturer['id_manufacturer'], 'small_default');
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['url'] = $this->context->link->getmanufacturerLink($manufacturer['id_manufacturer']);
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['nb_products'] = $manufacturer['nb_products'] > 1 ? ($this->trans('%number% products', array('%number%' => $manufacturer['nb_products']), 'Shop.Theme.Catalog')) : sprintf($this->trans('%number% product', array('%number%' => $manufacturer['nb_products']), 'Shop.Theme.Catalog'));
        }

        return $manufacturers_for_display;
    }

    public function getListingLabel()
    {
        return $this->label;
    }
}
