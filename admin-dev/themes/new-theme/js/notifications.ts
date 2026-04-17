/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Router from '@components/router';
import GlobalMap from './global-map';

const refreshNotifications = function (): void {
  let timer = null;
  let nbOrders = 0;
  let nbCustomers = 0;
  let nbCustomerMessages = 0;
  const router = new Router();

  $.ajax({
    type: 'POST',
    headers: {'cache-control': 'no-cache'},
    url: router.generate('admin_common_notifications'),
    async: true,
    cache: false,
    dataType: 'json',
    success(json) {
      if (json && Object.keys(json).length > 0) {
        if (Object.prototype.hasOwnProperty.call(json, 'order')) {
          nbOrders = parseInt(json.order.total, 10);

          fillTpl(
            json.order.results,
            $(GlobalMap.notifications.ordersNotifications),
            $(GlobalMap.notifications.orderNotificationTemplate).html(),
          );

          setNotificationsNumber('_nb_new_orders_', nbOrders);
        }

        if (Object.prototype.hasOwnProperty.call(json, 'customer')) {
          nbCustomers = parseInt(json.customer.total, 10);

          fillTpl(
            json.customer.results,
            $(GlobalMap.notifications.customersNotifications),
            $(GlobalMap.notifications.customerNotificationTemplate).html(),
          );

          setNotificationsNumber('_nb_new_customers_', nbCustomers);
        }

        if (Object.prototype.hasOwnProperty.call(json, 'customer_message')) {
          nbCustomerMessages = parseInt(json.customer_message.total, 10);

          fillTpl(
            json.customer_message.results,
            $(GlobalMap.notifications.messagesNotifications),
            $(GlobalMap.notifications.messageNotificationTemplate).html(),
          );
          setNotificationsNumber('_nb_new_messages_', nbCustomerMessages);
        }

        const notificationsTotal = nbOrders + nbCustomers + nbCustomerMessages;

        if (notificationsTotal) {
          $(GlobalMap.notifications.total)
            .removeClass('hide')
            .html(<string>(<unknown>notificationsTotal));
        } else if (!$('#notifications-total').hasClass('hide')) {
          $(GlobalMap.notifications.total).addClass('hide');
        }
      }
      timer = setTimeout(refreshNotifications, 120000);
    },
  });

  clearTimeout(<any>timer);
};

let fillTpl = function (
  results: Record<string, any>,
  eltAppendTo: JQuery,
  tpl: string,
) {
  eltAppendTo.children(GlobalMap.notifications.element).empty();
  if (results.length === 0) {
    eltAppendTo.addClass('empty');
    return;
  }

  eltAppendTo.removeClass('empty');
  $.each(results, (property, value) => {
    if (undefined === tpl) {
      return;
    }
    const router = new Router();

    /* eslint-disable max-len */
    eltAppendTo.children(GlobalMap.notifications.element).append(
      tpl
        .replace(/_id_order_/g, <string>(<unknown>parseInt(value.id_order, 10)))
        .replace(/_customer_name_/g, value.customer_name)
        .replace(/_iso_code_/g, value.iso_code)
        .replace(
          /_carrier_/g,
          value.carrier !== '' ? ` - ${value.carrier}` : '',
        )
        .replace(/_total_paid_/g, value.total_paid)
        .replace(
          /_id_customer_/g,
          <string>(<unknown>parseInt(value.id_customer, 10)),
        )
        .replace(
          /_company_/g,
          value.company !== '' ? ` (${value.company}) ` : '',
        )
        .replace(/_date_add_/g, value.date_add)
        .replace(/_status_/g, value.status)
        .replace(
          /order_url/g,
          value.id_order ? router.generate('admin_orders_view', {
            orderId: parseInt(value.id_order, 10),
          }) : '',
        )
        .replace(
          /customer_url/g,
          value.id_customer ? router.generate('admin_customers_view', {
            customerId: parseInt(value.id_customer, 10),
          }) : '',
        )
        .replace(
          /message_url/g,
          value.id_customer_thread ? router.generate('admin_customer_threads_view', {
            customerThreadId: parseInt(value.id_customer_thread, 10),
          }) : '',
        ),
    );
    /* eslint-ensable max-len */
  });
};

let setNotificationsNumber = function (id: string, number: number): void {
  if (number > 0) {
    $(`#${id}`).text(` (${number})`);
  } else {
    $(`#${id}`).text('');
  }
};

export default refreshNotifications;
