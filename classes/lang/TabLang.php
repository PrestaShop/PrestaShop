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

class TabLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Admin.Navigation.Menu';

    protected $keys = array('id_tab');

    protected $fieldsToUpdate = array('name');

    protected function init()
    {
        $this->fieldNames = array(
            'name' => array(
                md5('Sell') => $this->translator->trans('Sell', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Improve') => $this->translator->trans('Improve', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Configure') => $this->translator->trans('Configure', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('More') => $this->translator->trans('More', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Addresses') => $this->translator->trans('Addresses', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Administration') => $this->translator->trans('Administration', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Modules & Services') => $this->translator->trans('Modules & Services', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Advanced Parameters') => $this->translator->trans('Advanced Parameters', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Files') => $this->translator->trans('Files', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Attributes & Features') => $this->translator->trans('Attributes & Features', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Attributes') => $this->translator->trans('Attributes', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Carriers') => $this->translator->trans('Carriers', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Carrier') => $this->translator->trans('Carrier', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Cart Rules') => $this->translator->trans('Cart Rules', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Catalog Price Rules') => $this->translator->trans('Catalog Price Rules', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Catalog') => $this->translator->trans('Catalog', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Categories') => $this->translator->trans('Categories', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Page Categories') => $this->translator->trans('Page Categories', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Pages') => $this->translator->trans('Pages', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Combinations Generator') => $this->translator->trans('Combinations Generator', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Configuration') => $this->translator->trans('Configuration', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Contact') => $this->translator->trans('Contact', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Contacts') => $this->translator->trans('Contacts', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Countries') => $this->translator->trans('Countries', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Credit Slips') => $this->translator->trans('Credit Slips', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Import') => $this->translator->trans('Import', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Currencies') => $this->translator->trans('Currencies', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Customer Service') => $this->translator->trans('Customer Service', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Customer Settings') => $this->translator->trans('Customer Settings', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Customers') => $this->translator->trans('Customers', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Dashboard') => $this->translator->trans('Dashboard', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Database') => $this->translator->trans('Database', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('DB Backup') => $this->translator->trans('DB Backup', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Delivery Slips') => $this->translator->trans('Delivery Slips', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('E-mail') => $this->translator->trans('E-mail', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Employees') => $this->translator->trans('Employees', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Team') => $this->translator->trans('Team', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Features') => $this->translator->trans('Features', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('General') => $this->translator->trans('General', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Geolocation') => $this->translator->trans('Geolocation', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Groups') => $this->translator->trans('Groups', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Image Settings') => $this->translator->trans('Image Settings', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Images') => $this->translator->trans('Images', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Information') => $this->translator->trans('Information', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Instant Stock Status') => $this->translator->trans('Instant Stock Status', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('International') => $this->translator->trans('International', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Invoices') => $this->translator->trans('Invoices', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Languages') => $this->translator->trans('Languages', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Localization') => $this->translator->trans('Localization', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Locations') => $this->translator->trans('Locations', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Login') => $this->translator->trans('Login', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Logs') => $this->translator->trans('Logs', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Design') => $this->translator->trans('Design', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Maintenance') => $this->translator->trans('Maintenance', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Brands & Suppliers') => $this->translator->trans('Brands & Suppliers', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Brands') => $this->translator->trans('Brands', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Marketing') => $this->translator->trans('Marketing', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Menus') => $this->translator->trans('Menus', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Merchandise Returns') => $this->translator->trans('Merchandise Returns', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Modules') => $this->translator->trans('Modules', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Monitoring') => $this->translator->trans('Monitoring', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Multistore') => $this->translator->trans('Multistore', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Order Messages') => $this->translator->trans('Order Messages', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Order Settings') => $this->translator->trans('Order Settings', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Orders') => $this->translator->trans('Orders', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Outstanding') => $this->translator->trans('Outstanding', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Payment Methods') => $this->translator->trans('Payment Methods', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Preferences') => $this->translator->trans('Preferences', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Payment') => $this->translator->trans('Payment', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Performance') => $this->translator->trans('Performance', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Permissions') => $this->translator->trans('Permissions', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Positions') => $this->translator->trans('Positions', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Discounts') => $this->translator->trans('Discounts', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Product Settings') => $this->translator->trans('Product Settings', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Products') => $this->translator->trans('Products', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Profiles') => $this->translator->trans('Profiles', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Quick Access') => $this->translator->trans('Quick Access', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Referrers') => $this->translator->trans('Referrers', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Search') => $this->translator->trans('Search', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Search Engines') => $this->translator->trans('Search Engines', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('SEO & URLs') => $this->translator->trans('SEO & URLs', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Shipping') => $this->translator->trans('Shipping', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Shop Parameters') => $this->translator->trans('Shop Parameters', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Shop URLs') => $this->translator->trans('Shop URLs', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Shopping Carts') => $this->translator->trans('Shopping Carts', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Shops') => $this->translator->trans('Shops', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('SQL Manager') => $this->translator->trans('SQL Manager', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('States') => $this->translator->trans('States', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stats') => $this->translator->trans('Stats', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Statuses') => $this->translator->trans('Statuses', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stock Coverage') => $this->translator->trans('Stock Coverage', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stock Management') => $this->translator->trans('Stock Management', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stock Movement') => $this->translator->trans('Stock Movement', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stock') => $this->translator->trans('Stock', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Stores') => $this->translator->trans('Stores', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Suppliers') => $this->translator->trans('Suppliers', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Supply orders') => $this->translator->trans('Supply orders', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Tags') => $this->translator->trans('Tags', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Taxes') => $this->translator->trans('Taxes', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Tax Rules') => $this->translator->trans('Tax Rules', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Theme Catalog') => $this->translator->trans('Theme Catalog', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Theme & Logo') => $this->translator->trans('Theme & Logo', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Titles') => $this->translator->trans('Titles', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Traffic & SEO') => $this->translator->trans('Traffic & SEO', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Translations') => $this->translator->trans('Translations', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Warehouses') => $this->translator->trans('Warehouses', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Webservice') => $this->translator->trans('Webservice', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Zones') => $this->translator->trans('Zones', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Modules Catalog') => $this->translator->trans('Modules Catalog', array(), 'Admin.Navigation.Menu', $this->locale),

                // subtab
                md5('Selection') => $this->translator->trans('Selection', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Installed modules') => $this->translator->trans('Installed modules', array(), 'Admin.Navigation.Menu', $this->locale),
                md5('Notifications') => $this->translator->trans('Notifications', array(), 'Admin.Navigation.Menu', $this->locale),
            ),
        );
    }
}
