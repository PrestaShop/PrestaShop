/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {Grid} from '@PSTypes/grid';
import GridMap from '@components/grid/grid-map';

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
  extend(grid: Grid): void {
    grid
      .getHeaderContainer()
      .on('click', GridMap.actions.showQuery, () => this.onShowSqlQueryClick(grid));
    grid
      .getHeaderContainer()
      .on('click', GridMap.actions.exportQuery, () => this.onExportSqlManagerClick(grid));
  }

  /**
   * Invoked when clicking on the "show sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  onShowSqlQueryClick(grid: Grid): void {
    const $sqlManagerForm = $(GridMap.actions.showModalForm(grid.getId()));
    const $modal = $(GridMap.actions.showModalGrid(grid.getId()));

    if (this.isServerSideExport(grid)) {
      // The SQL is no longer in the page, so fetch it from the authorized endpoint for preview.
      $.post($sqlManagerForm.attr('action'), $sqlManagerForm.serialize(), (response: {sql: string}) => {
        $sqlManagerForm.find('textarea[name="sql"]').val(response.sql);
        $sqlManagerForm.find('input[name="name"]').val(this.getNameFromBreadcrumb());
      });
    } else {
      this.fillExportForm($sqlManagerForm, grid);
    }

    $modal.modal('show');

    $modal.on('click', GridMap.sqlSubmit, () => $sqlManagerForm.submit());
  }

  /**
   * Invoked when clicking on the "export to the sql query" toolbar button
   *
   * @param {Grid} grid
   *
   * @private
   */
  private onExportSqlManagerClick(grid: Grid): void {
    const $sqlManagerForm = $(GridMap.actions.showModalForm(grid.getId()));

    if (this.isServerSideExport(grid)) {
      // The endpoint regenerates the SQL server-side; only the request name is taken from the page.
      $sqlManagerForm.find('input[name="name"]').val(this.getNameFromBreadcrumb());
    } else {
      this.fillExportForm($sqlManagerForm, grid);
    }

    $sqlManagerForm.submit();
  }

  /**
   * Whether this grid regenerates its SQL server-side (opted in via
   * SqlExportableGridDefinitionFactoryInterface) instead of embedding it in the page.
   *
   * @param {Grid} grid
   *
   * @return {boolean}
   *
   * @private
   */
  private isServerSideExport(grid: Grid): boolean {
    return Boolean(
      grid
        .getContainer()
        .find(GridMap.gridTable)
        .data('sqlExportGridFactory'),
    );
  }

  /**
   * Fill export form with SQL and it's name
   *
   * @param {jQuery} $sqlManagerForm
   * @param {Grid} grid
   *
   * @private
   */
  private fillExportForm($sqlManagerForm: JQuery, grid: Grid) {
    const query = grid
      .getContainer()
      .find(GridMap.gridTable)
      .data('query');

    $sqlManagerForm.find('textarea[name="sql"]').val(query);
    $sqlManagerForm
      .find('input[name="name"]')
      .val(this.getNameFromBreadcrumb());
  }

  /**
   * Get export name from page's breadcrumb
   *
   * @return {String}
   *
   * @private
   */
  private getNameFromBreadcrumb(): string {
    const $breadcrumbs = $(GridMap.headerToolbar).find(GridMap.breadcrumbItem);
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
