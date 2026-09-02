/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

/**
 * Allow to display the last SQL query in a modal and redirect to SQL Manager.
 */
class SqlManager {
  showLastSqlQuery() {
    $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody.sql-manager').data('query'));
    $('#catalog_sql_query_modal .btn-sql-submit').on('click', () => {
      $('#catalog_sql_query_modal_content').submit();
    });
    $('#catalog_sql_query_modal').modal('show');
  }

  sendLastSqlQuery(name) {
    $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody.sql-manager').data('query'));
    $('#catalog_sql_query_modal_content input[name="name"]').val(name);
    $('#catalog_sql_query_modal_content').submit();
  }

  createSqlQueryName() {
    let container = false;
    let current = false;

    if ($('.breadcrumb')) {
      container = $('.breadcrumb li').eq(0).text().replace(/\s+/g, ' ')
        .trim();
      current = $('.breadcrumb li').eq(-1).text().replace(/\s+/g, ' ')
        .trim();
    }
    let title = false;

    if ($('h2.title')) {
      title = $('h2.title').first().text().replace(/\s+/g, ' ')
        .trim();
    }

    let name = false;

    if (container && current && container !== current) {
      name = `${container} > ${current}`;
    } else if (container) {
      name = container;
    } else if (current) {
      name = current;
    }

    if (title && title !== current && title !== container) {
      if (name) {
        name = `${name} > ${title}`;
      } else {
        name = title;
      }
    }

    return name.trim();
  }
}

export default SqlManager;
