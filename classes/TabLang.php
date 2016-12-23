<?php
/**
 * 2007-2016 PrestaShop
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Symfony\Component\Translation\TranslatorInterface;

class TabLangCore
{
    /** @var TranslatorInterface  */
    private $translator;

    /** @var string */
    private $locale;

    /** @var array */
    private $tabNames;

    public function __construct(TranslatorInterface $translator, $locale)
    {
        $this->translator = $translator;
        $this->locale = $locale;
        $this->init();
    }

    public function getName($classname)
    {
        if (isset($this->tabNames[$classname])) {
            return $this->tabNames[$classname];
        }

        return $classname;
    }

    private function init()
    {
        $this->tabNames = array(

            'AdminDashboard' => $this->translator->trans('Dashboard', array(), 'Admin.Navigation.Menu', $this->locale),
            'SELL' => $this->translator->trans('Sell', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentOrders' => $this->translator->trans('Orders', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminOrders' => $this->translator->trans('Orders', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminInvoices' => $this->translator->trans('Invoices', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSlip' => $this->translator->trans('Credit Slips', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminDeliverySlip' => $this->translator->trans('Delivery Slips', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCarts' => $this->translator->trans('Shopping Carts', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCatalog' => $this->translator->trans('Catalog', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminProducts' => $this->translator->trans('Products', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCategories' => $this->translator->trans('Categories', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminTracking' => $this->translator->trans('Monitoring', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentAttributesGroups' => $this->translator->trans('Attributes & Features', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAttributesGroups' => $this->translator->trans('Attributes', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminFeatures' => $this->translator->trans('Features', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentManufacturers' => $this->translator->trans('Brands & Suppliers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminManufacturers' => $this->translator->trans('Manufacturers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSuppliers' => $this->translator->trans('Suppliers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAttachments' => $this->translator->trans('Files', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentCartRules' => $this->translator->trans('Discounts', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCartRules' => $this->translator->trans('Cart Rules', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSpecificPriceRule' => $this->translator->trans('Catalog Price Rules', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentCustomer' => $this->translator->trans('Customers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCustomers' => $this->translator->trans('Customers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAddresses' => $this->translator->trans('Addresses', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminOutstanding' => $this->translator->trans('Outstanding', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentCustomerThreads' => $this->translator->trans('Customer Service', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCustomerThreads' => $this->translator->trans('Customer Service', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminOrderMessage' => $this->translator->trans('Order Messages', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminReturn' => $this->translator->trans('Merchandise Returns', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStats' => $this->translator->trans('Stats', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminWarehouses' => $this->translator->trans('Warehouses', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentStockManagement' => $this->translator->trans('Stock Management', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStockManagement' => $this->translator->trans('Stock Management', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStockMvt' => $this->translator->trans('Stock Movement', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStockInstantState' => $this->translator->trans('Instant Stock Status', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStockCover' => $this->translator->trans('Stock Coverage', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSupplyOrders' => $this->translator->trans('Supply orders', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStockConfiguration' => $this->translator->trans('Configuration', array(), 'Admin.Navigation.Menu', $this->locale),
            'IMPROVE' => $this->translator->trans('Improve', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentModulesSf' => $this->translator->trans('Modules', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminModulesSf' => $this->translator->trans('Modules & Services', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAddonsCatalog' => $this->translator->trans('Modules Catalog', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentThemes' => $this->translator->trans('Design', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminThemes' => $this->translator->trans('Theme & Logo', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminThemesCatalog' => $this->translator->trans('Theme Catalog', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCmsContent' => $this->translator->trans('Pages', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminModulesPositions' => $this->translator->trans('Positions', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminImages' => $this->translator->trans('Image Settings', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentShipping' => $this->translator->trans('Shipping', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCarriers' => $this->translator->trans('Carriers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminShipping' => $this->translator->trans('Preferences', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentPayment' => $this->translator->trans('Payment', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminPayment' => $this->translator->trans('Payment Methods', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminPaymentPreferences' => $this->translator->trans('Preferences', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminInternational' => $this->translator->trans('International', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentLocalization' => $this->translator->trans('Localization', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminLocalization' => $this->translator->trans('Localization', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminLanguages' => $this->translator->trans('Languages', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCurrencies' => $this->translator->trans('Currencies', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminGeolocation' => $this->translator->trans('Geolocation', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentCountries' => $this->translator->trans('Locations', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCountries' => $this->translator->trans('Countries', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminZones' => $this->translator->trans('Zones', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStates' => $this->translator->trans('States', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentTaxes' => $this->translator->trans('Taxes', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminTaxes' => $this->translator->trans('Taxes', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminTaxRulesGroup' => $this->translator->trans('Tax Rules', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminTranslations' => $this->translator->trans('Translations', array(), 'Admin.Navigation.Menu', $this->locale),
            'CONFIGURE' => $this->translator->trans('Configure', array(), 'Admin.Navigation.Menu', $this->locale),
            'ShopParameters' => $this->translator->trans('Shop Parameters', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentPreferences' => $this->translator->trans('General', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminPreferences' => $this->translator->trans('General', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminMaintenance' => $this->translator->trans('Maintenance', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentOrderPreferences' => $this->translator->trans('Order Settings', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminOrderPreferences' => $this->translator->trans('Order Settings', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStatuses' => $this->translator->trans('Statuses', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminPPreferences' => $this->translator->trans('Product Settings', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentCustomerPreferences' => $this->translator->trans('Customer Settings', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminCustomerPreferences' => $this->translator->trans('Customers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminGroups' => $this->translator->trans('Groups', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminGenders' => $this->translator->trans('Titles', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentStores' => $this->translator->trans('Contact', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminContacts' => $this->translator->trans('Contacts', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminStores' => $this->translator->trans('Stores', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentMeta' => $this->translator->trans('Traffic', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminMeta' => $this->translator->trans('SEO & URLs', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSearchEngines' => $this->translator->trans('Search Engines', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminReferrers' => $this->translator->trans('Referrers', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentSearchConf' => $this->translator->trans('Search', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminSearchConf' => $this->translator->trans('Search', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminTags' => $this->translator->trans('Tags', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAdvancedParameters' => $this->translator->trans('Advanced Parameters', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminInformation' => $this->translator->trans('Information', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminPerformance' => $this->translator->trans('Performance', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAdminPreferences' => $this->translator->trans('Administration', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminEmails' => $this->translator->trans('E-mail', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminImport' => $this->translator->trans('Import', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentEmployees' => $this->translator->trans('Team', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminEmployees' => $this->translator->trans('Employees', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminProfiles' => $this->translator->trans('Profiles', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminAccess' => $this->translator->trans('Permissions', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminParentRequestSql' => $this->translator->trans('Database', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminRequestSql' => $this->translator->trans('SQL Manager', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminBackup' => $this->translator->trans('DB Backup', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminLogs' => $this->translator->trans('Logs', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminWebservice' => $this->translator->trans('Webservice', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminShopGroup' => $this->translator->trans('Multistore', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminShopUrl' => $this->translator->trans('Multistore', array(), 'Admin.Navigation.Menu', $this->locale),
            'AdminQuickAccesses' => $this->translator->trans('Quick Access', array(), 'Admin.Navigation.Menu', $this->locale),
            'DEFAULT' => $this->translator->trans('More', array(), 'Admin.Navigation.Menu', $this->locale),
        );
    }
}
