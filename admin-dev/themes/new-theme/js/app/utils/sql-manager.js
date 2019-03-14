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

const $ = global.$;

/**
 * Allow to display the last SQL query in a modal and redirect to SQL Manager.
 */
class SqlManager {
  showLastSqlQuery() {
    $('#catalog_sql_query_modal_content textarea[name="sql"]').val($('tbody.sql-manager').data('query'));
    $('#catalog_sql_query_modal .btn-sql-submit').click(() => {
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
      container = $('.breadcrumb li').eq(0).text().replace(/\s+/g, ' ').trim();
      current = $('.breadcrumb li').eq(-1).text().replace(/\s+/g, ' ').trim();
    }
    let title = false;
    if ($('h2.title')) {
      title = $('h2.title').first().text().replace(/\s+/g, ' ').trim();
    }

    let name = false;
    if (container && current && container != current) {
      name = container + ' > ' + current;
    } else if (container) {
      name = container;
    } else if (current) {
      name = current;
    }

    if (title && title != current && title != container) {
      if (name) {
        name = name + ' > ' + title;
      } else {
        name = title;
      }
    }

    return name.trim();
  }
}

export default SqlManager;
