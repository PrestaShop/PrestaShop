<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\KeysReference\Xlang;

use PrestaShopBundle\Translation\TranslatorComponent as Translator;

class TabLang
{
    /** @var Translator  */
    protected $translator;

    /** @var string */
    protected $locale;

    protected function init()
    {
         $this->translator->trans('Sell', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Improve', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Configure', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('More', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Addresses', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Administration', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Modules & Services', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Advanced Parameters', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Files', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Attributes & Features', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Attributes', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Carriers', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Carrier', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Cart Rules', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Catalog Price Rules', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Catalog', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Categories', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Page Categories', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Pages', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Combinations Generator', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Configuration', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Contact', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Contacts', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Countries', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Credit Slips', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Import', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Currencies', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Customer Service', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Customer Settings', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Customers', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Dashboard', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Database', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('DB Backup', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Delivery Slips', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('E-mail', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Employees', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Team', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Features', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('General', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Geolocation', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Groups', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Image Settings', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Images', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Information', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Instant Stock Status', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('International', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Invoices', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Languages', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Localization', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Locations', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Login', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Logs', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Design', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Maintenance', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Brands & Suppliers', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Brands', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Marketing', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Menus', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Merchandise Returns', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Modules', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Monitoring', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Multistore', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Order Messages', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Order Settings', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Orders', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Outstanding', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Payment Methods', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Preferences', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Payment', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Performance', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Permissions', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Positions', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Discounts', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Product Settings', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Products', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Profiles', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Quick Access', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Referrers', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Search', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Search Engines', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('SEO & URLs', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Shipping', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Shop Parameters', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Shop URLs', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Shopping Carts', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Shops', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('SQL Manager', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('States', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stats', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Statuses', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stock Coverage', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stock Management', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stock Movement', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stock', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Stores', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Suppliers', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Supply orders', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Tags', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Taxes', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Tax Rules', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Theme Catalog', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Theme & Logo', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Titles', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Traffic & SEO', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Translations', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Warehouses', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Webservice', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Zones', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Modules Catalog', array(), 'Admin.Navigation.Menu', $this->locale);

        // subtab
         $this->translator->trans('Selection', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Installed modules', array(), 'Admin.Navigation.Menu', $this->locale);
         $this->translator->trans('Notifications', array(), 'Admin.Navigation.Menu', $this->locale);
    }
}
