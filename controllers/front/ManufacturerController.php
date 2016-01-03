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

use PrestaShop\PrestaShop\Core\Business\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Business\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;

class ManufacturerControllerCore extends ProductListingFrontController
{
    public $php_self = 'manufacturer';

    protected $manufacturer;

    public function canonicalRedirection($canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->manufacturer)) {
            parent::canonicalRedirection($this->context->link->getManufacturerLink($this->manufacturer));
        }
    }

    /**
     * Initialize manufaturer controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        if ($id_manufacturer = Tools::getValue('id_manufacturer')) {
            $this->manufacturer = new Manufacturer((int)$id_manufacturer, $this->context->language->id);

            if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop()) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->canonicalRedirection();
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if (Configuration::get('PS_DISPLAY_SUPPLIERS')) {
            parent::initContent();

            if (Validate::isLoadedObject($this->manufacturer) && $this->manufacturer->active && $this->manufacturer->isAssociatedToShop()) {
                $this->assignManufacturer();
                $this->doProductSearch('catalog/manufacturer.tpl');
            } else {
                $this->assignAll();
                $this->setTemplate('catalog/manufacturers.tpl');
            }
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery;
        $query
            ->setIdManufacturer($this->manufacturer->id)
            ->setSortOrder(new SortOrder('product', 'position', 'asc'))
        ;
        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        $translator = new Translator(new LegacyContext);
        return new ManufacturerProductSearchProvider(
            $translator,
            $this->manufacturer
        );
    }

    /**
     * Assign template vars if displaying one manufacturer
     */
    protected function assignManufacturer()
    {
        $this->context->smarty->assign([
            'manufacturer' => $this->objectSerializer->toArray($this->manufacturer),
        ]);
    }

    /**
     * Assign template vars if displaying the manufacturer list
     */
    protected function assignAll()
    {
        $this->context->smarty->assign([
            'manufacturers' => $this->getTemplateVarManufacturers(),
        ]);
    }

    public function getTemplateVarManufacturers()
    {
        $manufacturers = Manufacturer::getManufacturers(true, $this->context->language->id, true, $this->p, $this->n, false);
        $manufacturers_for_display = [];

        foreach ($manufacturers as $manufacturer) {
            $manufacturers_for_display[$manufacturer['id_manufacturer']] = $manufacturer;
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['text'] = $manufacturer['short_description'];
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['image'] = _THEME_MANU_DIR_.$manufacturer['id_manufacturer'].'-medium_default.jpg';
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['url'] = $this->context->link->getmanufacturerLink($manufacturer['id_manufacturer']);
            $manufacturers_for_display[$manufacturer['id_manufacturer']]['nb_products'] = $manufacturer['nb_products'] > 1 ? sprintf($this->l('%s products'), $manufacturer['nb_products']) : sprintf($this->l('% product'), $manufacturer['nb_products']);
        }

        return $manufacturers_for_display;
    }
}
