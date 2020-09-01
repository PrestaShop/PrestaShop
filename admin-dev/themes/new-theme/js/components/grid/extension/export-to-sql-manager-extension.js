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

const {$} = window;

/**
 * Class ExportToSqlManagerExtension extends grid with exporting query to SQL Manager
 */
export default class ExportToSqlManagerExtension {
  /**
   * Extend grid
   *
   * @param {Grid} grid
   */
  extend(grid) {
    grid.getHeaderContainer().on('click', '.js-common_show_query-grid-action', () => this.onShowSqlQueryClick(grid));
    grid.getHeaderContainer().on(
      'click',
      '.js-common_export_sql_manager-grid-action',
      () => this.onExportSqlManagerClick(grid),
    );
  }

  /**
   * Invoked when clicking on the "show sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  onShowSqlQueryClick(grid) {
    const $sqlManagerForm = $(`#${grid.getId()}_common_show_query_modal_form`);
    this.fillExportForm($sqlManagerForm, grid);

    const $modal = $(`#${grid.getId()}_grid_common_show_query_modal`);
    $modal.modal('show');

    $modal.on('click', '.btn-sql-submit', () => $sqlManagerForm.submit());
  }

  /**
   * Invoked when clicking on the "export to the sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  onExportSqlManagerClick(grid) {
    const $sqlManagerForm = $(`#${grid.getId()}_common_show_query_modal_form`);

    this.fillExportForm($sqlManagerForm, grid);

    $sqlManagerForm.submit();
  }

  /**
   * Fill export form with SQL and it's name
   *
   * @param {jQuery} $sqlManagerForm
   * @param {Grid} grid
   *
   * @private
   */
  fillExportForm($sqlManagerForm, grid) {
    const query = grid.getContainer().find('.js-grid-table').data('query');

    $sqlManagerForm.find('textarea[name="sql"]').val(query);
    $sqlManagerForm.find('input[name="name"]').val(this.getNameFromBreadcrumb());
  }

  /**
   * Get export name from page's breadcrumb
   *
   * @return {String}
   *
   * @private
   */
  getNameFromBreadcrumb() {
    const $breadcrumbs = $('.header-toolbar').find('.breadcrumb-item');
    let name = '';

    $breadcrumbs.each((i, item) => {
      const $breadcrumb = $(item);

      const breadcrumbTitle = $breadcrumb.find('a').length > 0
        ? $breadcrumb.find('a').text()
        : $breadcrumb.text();

      if (name.length > 0) {
        name = name.concat(' > ');
      }

      name = name.concat(breadcrumbTitle);
    });

    return name;
  }
}
