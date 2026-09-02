/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

class SqlManagerPage {
  constructor() {
    const requestSqlGrid = new window.prestashop.component.Grid('sql_request');
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    requestSqlGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

    $(document).on('change', '.js-db-tables-select', () => this.reloadDbTableColumns(),
    );
    $(document).on('click', '.js-add-db-table-to-query-btn', (event: JQueryEventObject) => this.addDbTableToQuery(event),
    );
    $(document).on('click', '.js-add-db-table-column-to-query-btn', (event: JQueryEventObject) => {
      this.addDbTableColumnToQuery(event);
    });
  }

  /**
   * Reload database table columns
   */
  reloadDbTableColumns() {
    const $selectedOption = $('.js-db-tables-select').find('option:selected');
    const $table = $('.js-table-columns');

    $.ajax($selectedOption.data('table-columns-url')).then((response) => {
      $('.js-table-alert').addClass('d-none');

      const {columns} = response;

      $table.removeClass('d-none');
      $table.find('tbody').empty();

      columns.forEach((column: Record<string, any>) => {
        const $row = $('<tr>')
          .append($('<td>').html(column.name))
          .append($('<td>').html(column.type))
          .append(
            $('<td>')
              .addClass('text-right')
              .append(
                $('<button>')
                  .addClass(
                    'btn btn-sm btn-outline-secondary js-add-db-table-column-to-query-btn',
                  )
                  .attr('data-column', column.name)
                  .html($table.data('action-btn')),
              ),
          );

        $table.find('tbody').append($row);
      });
    });
  }

  /**
   * Add selected database table name to SQL query input
   *
   * @param event
   */
  addDbTableToQuery(event: JQueryEventObject): void {
    const $selectedOption = $('.js-db-tables-select').find('option:selected');

    if ($selectedOption.length === 0) {
      alert($(event.target).data('choose-table-message'));

      return;
    }

    this.addToQuery(<string>$selectedOption.val());
  }

  /**
   * Add table column to SQL query input
   *
   * @param event
   */
  addDbTableColumnToQuery(event: JQueryEventObject): void {
    this.addToQuery($(event.target).data('column'));
  }

  /**
   * Add data to SQL query input
   *
   * @param {String} data
   */
  addToQuery(data: string): void {
    const $queryInput = $('#sql_request_sql');
    $queryInput.val(`${$queryInput.val()} ${data}`);
  }
}

$(() => {
  new SqlManagerPage();
});
