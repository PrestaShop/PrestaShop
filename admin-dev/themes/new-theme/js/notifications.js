/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import Router from './components/router';

const refreshNotifications = function () {
  let timer = null;
  $.ajax({
    type: 'POST',
    headers: {'cache-control': 'no-cache'},
    url: `${window.admin_notification_get_link}&rand=${new Date().getTime()}`,
    async: true,
    cache: false,
    dataType: 'json',
    success(json) {
      if (json) {
        const nbOrders = parseInt(json.order.total, 10);
        const nbCustomers = parseInt(json.customer.total, 10);
        const nbCustomerMessages = parseInt(json.customer_message.total, 10);
        const notificationsTotal = nbOrders + nbCustomers + nbCustomerMessages;

        fillTpl(json.order.results, $('#orders-notifications'), $('#order-notification-template').html());
        fillTpl(json.customer.results, $('#customers-notifications'), $('#customer-notification-template').html());
        fillTpl(
          json.customer_message.results,
          $('#messages-notifications'),
          $('#message-notification-template').html(),
        );

        setNotificationsNumber('_nb_new_orders_', nbOrders);
        setNotificationsNumber('_nb_new_customers_', nbCustomers);
        setNotificationsNumber('_nb_new_messages_', nbCustomerMessages);
        if (notificationsTotal) {
          $('#notifications-total').removeClass('hide').html(notificationsTotal);
        } else {
          $('#notifications-total').remove();
        }
      }
      timer = setTimeout(refreshNotifications, 120000);
    },
  });

  clearTimeout(timer);
};
let fillTpl = function (results, eltAppendTo, tpl) {
  eltAppendTo.children('.notification-elements').empty();
  if (results.length === 0) {
    eltAppendTo.addClass('empty');
    return;
  }
  $.each(results, function (property, value) {
    if (undefined === tpl) {
      return;
    }

    const router = new Router();
    let tplReplaced = '';
    let route = '';

    tplReplaced = tpl
      .replace(/_id_order_/g, parseInt(value.id_order, 10))
      .replace(/_customer_name_/g, value.customer_name)
      .replace(/_iso_code_/g, value.iso_code)
      .replace(/_carrier_/g, (value.carrier !== '' ? ` - ${value.carrier}` : ''))
      .replace(/_total_paid_/g, value.total_paid)
      .replace(/_company_/g, (value.company !== '' ? ` (${value.company}) ` : ''))
      .replace(/_date_add_/g, value.date_add)
      .replace(/_id_customer_/g, parseInt(value.id_customer, 10))
      .replace(/_company_/g, (value.company !== '' ? ` (${value.company}) ` : ''))
      .replace(/_date_add_/g, value.date_add)
      .replace(/_status_/g, value.status);

    switch (eltAppendTo[0].id) {
      case 'orders-notifications':
        tplReplaced = tplReplaced.replace(
          /order_url/g,
          `${window.baseAdminDir}index.php?tab=AdminOrders&token=${window.token_admin_orders}&vieworder&id_order=${value.id_order}`
        );
        break;
      case 'customers-notifications':
        const customerId = parseInt(value.id_customer, 10);
        route = router.generate('admin_customers_view', {customerId});
        tplReplaced = tplReplaced.replace(/customer_url/g, route);
        break;
      case 'messages-notifications':
        const customerThreadId = parseInt(value.id_customer_thread, 10);
        route = router.generate('admin_customer_threads_view', {customerThreadId});
        tplReplaced = tplReplaced.replace(/message_url/g, route);
        break;
      default:
        break;
    }

    eltAppendTo.children('.notification-elements').append(tplReplaced);
  });
};
let setNotificationsNumber = function (id, number) {
  if (number > 0) {
    $(`#${id}`).text(` (${number})`);
  } else {
    $(`#${id}`).text('');
  }
};

export default refreshNotifications;
