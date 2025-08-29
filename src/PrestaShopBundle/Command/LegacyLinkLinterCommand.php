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

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Routing\Linter\AdminRouteProvider;
use PrestaShopBundle\Routing\Linter\LegacyLinkLinter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Checks if all admin routes are configured with _legacy_link
 */
class LegacyLinkLinterCommand extends Command
{
    /**
     * @var LegacyLinkLinter
     */
    private $legacyLinkLinter;

    /**
     * @var AdminRouteProvider
     */
    private $adminRouteProvider;

    /**
     * The _legacy_link configuration is not relevant for these routes, no need to apply the linter on them
     */
    private const ROUTE_WHITE_LIST = [
        'admin_administration_notifications_save',
        'admin_administration_upload_quota_save',
        'admin_alias_search_search_term_action',
        'admin_all_attribute_groups',
        'admin_attribute_groups_update_position',
        'admin_backup_download',
        'admin_backups_download_view',
        'admin_cart_addresses_edit',
        'admin_cart_rules_search_grid',
        'admin_carts_add_cart_rule',
        'admin_carts_add_product',
        'admin_carts_delete_cart_rule',
        'admin_carts_delete_product',
        'admin_carts_edit_addresses',
        'admin_carts_edit_carrier',
        'admin_carts_edit_currency',
        'admin_carts_edit_language',
        'admin_carts_edit_product_price',
        'admin_carts_edit_product_quantity',
        'admin_carts_info',
        'admin_carts_set_delivery_settings',
        'admin_catalog_price_rules_list_for_product',
        'admin_categories_get_ajax_categories',
        'admin_categories_delete_thumbnail_image',
        'admin_categories_get_categories_tree',
        'admin_country_states',
        'admin_currencies_update_live_exchange_rates',
        'admin_customer_threads_filter',
        'admin_employees_get_tabs',
        'admin_feature_get_feature_values',
        'admin_import_data_configuration_index',
        'admin_import_process',
        'admin_international_translations_modify',
        'admin_link_block_create',
        'admin_link_block_create_process',
        'admin_link_block_delete',
        'admin_link_block_edit',
        'admin_link_block_edit_process',
        'admin_link_block_update_positions',
        'admin_localization_advanced_save',
        'admin_localization_local_units_save',
        'admin_logs_search',
        'admin_metas_seo_options_save',
        'admin_metas_shop_urls_save',
        'admin_metas_url_schema_save',
        'admin_module_configure_action',
        'admin_order_addresses_edit',
        'admin_order_invoices_generate_by_id',
        'admin_order_preferences_gift_options_save',
        'admin_orders_add_product',
        'admin_orders_cancellation',
        'admin_orders_change_currency',
        'admin_orders_change_customer_address',
        'admin_orders_change_orders_status',
        'admin_orders_configure_product_pagination',
        'admin_orders_delete_product',
        'admin_orders_display_customization_image',
        'admin_orders_duplicate_cart',
        'admin_orders_generate_delivery_slip_pdf',
        'admin_orders_generate_invoice',
        'admin_orders_generate_invoice_pdf',
        'admin_orders_get_discounts',
        'admin_orders_get_documents',
        'admin_orders_get_invoices',
        'admin_orders_get_payments',
        'admin_orders_get_prices',
        'admin_orders_get_products',
        'admin_orders_get_shipping',
        'admin_orders_list_update_status',
        'admin_orders_partial_refund',
        'admin_orders_place',
        'admin_orders_preview',
        'admin_orders_product_prices',
        'admin_orders_products_search',
        'admin_orders_return_product',
        'admin_orders_search',
        'admin_orders_send_message',
        'admin_orders_send_process_order_email',
        'admin_orders_standard_refund',
        'admin_orders_update_product',
        'admin_orders_update_shipping',
        'admin_orders_update_status',
        'admin_product_catalog',
        'admin_product_form',
        'admin_products_attribute_groups',
        'admin_products_combinations',
        'admin_products_combinations_bulk_combination_form',
        'admin_products_combinations_bulk_delete',
        'admin_products_combinations_bulk_edit_combination',
        'admin_products_combinations_delete_combination',
        'admin_products_combinations_edit_combination',
        'admin_products_combinations_generate',
        'admin_products_combinations_ids',
        'admin_products_combinations_update_combination_from_listing',
        'admin_products_delete_from_shop',
        'admin_products_delete_from_shop_group',
        'admin_products_disable_for_all_shops',
        'admin_products_disable_for_shop_group',
        'admin_products_download_virtual_product_file',
        'admin_products_enable_for_all_shops',
        'admin_products_enable_for_shop_group',
        'admin_products_grid_category_filter',
        'admin_products_grid_shop_previews',
        'admin_products_light_list',
        'admin_products_quantity',
        'admin_products_reset_grid_search',
        'admin_products_search',
        'admin_products_search_combinations_for_association',
        'admin_products_search_product_combinations',
        'admin_products_search_products_for_association',
        'admin_products_specific_prices_create',
        'admin_products_specific_prices_delete',
        'admin_products_specific_prices_edit',
        'admin_products_specific_prices_list',
        'admin_products_toggle_status_for_shop',
        'admin_security_sessions_customer_search',
        'admin_security_sessions_employee_search',
        'admin_servers',
        'admin_shipping_preferences_carrier_options_save',
        'admin_stock_movements_overview',
        'admin_employees_get_password_generated',
    ];

    /**
     * The _legacy_link configuration is not relevant for these routes, no need to apply the linter on them
     */
    private const CONTROLLER_WHITE_LIST = [
        'AdminAdminAPI',
    ];

    public function __construct(LegacyLinkLinter $legacyLinkLinter, AdminRouteProvider $adminRouteProvider)
    {
        parent::__construct();
        $this->legacyLinkLinter = $legacyLinkLinter;
        $this->adminRouteProvider = $adminRouteProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:linter:legacy-link')
            ->setDescription('Checks if _legacy_link is configured in BackOffice routes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unconfiguredRoutes = $this->getUnconfiguredRoutes();
        $io = new SymfonyStyle($input, $output);

        if (!empty($unconfiguredRoutes)) {
            $io->warning(sprintf(
                '%s routes are not configured with _legacy_link:',
                count($unconfiguredRoutes)
            ));
            $io->listing($unconfiguredRoutes);

            return 1;
        }

        $io->success('There is no routes without _legacy_link settings');

        return 0;
    }

    /**
     * Returns routes that are missing _legacy_link configuration
     *
     * @return array
     */
    private function getUnconfiguredRoutes()
    {
        $routes = $this->adminRouteProvider->getRoutes();
        $unconfiguredRoutes = [];

        foreach ($routes as $routeName => $route) {
            if (in_array($routeName, self::ROUTE_WHITE_LIST) || true === $this->legacyLinkLinter->lint($routeName, $route)) {
                continue;
            }
            if ($route->getDefault('_legacy_controller') && in_array($route->getDefault('_legacy_controller'), self::CONTROLLER_WHITE_LIST)) {
                continue;
            }
            $unconfiguredRoutes[] = $routeName;
        }

        return $unconfiguredRoutes;
    }
}
