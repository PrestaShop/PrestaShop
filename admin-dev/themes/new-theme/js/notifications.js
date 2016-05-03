(function headerNotifications(global, jQuery) {
  let refreshNotifications = function () {
    let timer = null;

    jQuery.ajax({
      type: 'POST',
      headers: {"cache-control": "no-cache"},
      url: baseAdminDir + 'ajax.php?rand=' + new Date().getTime(),
      async: true,
      cache: false,
      dataType: 'json',
      data: {"getNotifications": "1"},
      success: function (json) {
        if (json) {
          let nbOrders = parseInt(json.order.total);
          let nbCustomers = parseInt(json.customer.total);
          let nbCustomerMessages = parseInt(json.customer_message.total);
          let notifications_total = nbOrders + nbCustomers + nbCustomerMessages;

          fillTpl(json.order.results, jQuery("#orders-notifications"), jQuery("#order-notification-template").html());
          fillTpl(json.customer.results, jQuery("#customers-notifications"), jQuery("#customer-notification-template").html());
          fillTpl(json.customer_message.results, jQuery("#messages-notifications"), jQuery("#message-notification-template").html());

          setNotificationsNumber(jQuery('.notifications #orders-tab'), "_nb_new_orders_", nbOrders);
          setNotificationsNumber(jQuery('.notifications #customers-tab'), "_nb_new_customers_", nbCustomers);
          setNotificationsNumber(jQuery('.notifications #messages-tab'), "_nb_new_messages_", nbCustomerMessages);
          jQuery('#orders_notif_value').html(notifications_total);
        }
        //timer = setTimeout(refreshNotifications, 5000);
      }
    });

    clearTimeout(timer);
  }

  let fillTpl = function (results, eltAppendTo, tpl) {
    eltAppendTo.empty();
    jQuery.each(results, function(property, value) {
      eltAppendTo.append(
        tpl.replace(/_id_order_/g, parseInt(value.id_order))
          .replace(/_customer_name_/g, value.customer_name)
          .replace(/_iso_code_/g, value.iso_code)
          .replace(/_carrier_/g, (value.carrier !== "" ? " - " + value.carrier : ""))
          .replace(/_total_paid_/g, value.total_paid)
          .replace(/_id_customer_/g, parseInt(value.id_customer))
          .replace(/_company_/g, (value.company !== "" ? " (" + value.company + ") " : ""))
          .replace(/_date_add_/g, value.date_add)
          .replace(/_status_/g, value.status)
      );
    });
  }

  let setNotificationsNumber = function (elt, id, number) {
    if (number > 0) {
      elt.html((elt.html().replace(id, " (" + number + ")")));
    } else {
      elt.html((elt.html().replace(id, "")));
    }
  }

  refreshNotifications();
}(window, $));
