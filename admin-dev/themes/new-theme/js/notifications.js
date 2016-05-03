(function headerNotifications(global, jQuery) {
  jQuery.ajax({
    type: 'POST',
    headers: {"cache-control": "no-cache"},
    url: baseAdminDir+'ajax.php?rand=' + new Date().getTime(),
    async: true,
    cache: false,
    dataType : 'json',
    data: {"getNotifications" : "1"},
    success: function(json) {
      if (json) {

        let orderTpl = jQuery("#order-notification-template").html();
        let customerTpl = jQuery("#customer-notification-template").html();
        let messageTpl = jQuery("#message-notification-template").html();

        let nbOrders = parseInt(json.order.total);
        let nbCustomers = parseInt(json.customer.total);
        let nbCustomerMessages = parseInt(json.customer_message.total);
        let notifications_total = nbOrders + nbCustomers + nbCustomerMessages;

        jQuery.each(json.order.results, function(property, value) {
          jQuery("#orders-notifications").append(
            orderTpl.replace(/_id_order_/g, parseInt(value.id_order))
              .replace(/_customer_name_/g, value.customer_name)
              .replace(/_iso_code_/g, value.iso_code)
              .replace(/_carrier_/g, (value.carrier !== "" ? " - " + value.carrier : ""))
              .replace(/_total_paid_/g, value.total_paid)
          );
        });

        jQuery.each(json.customer.results, function(property, value) {
          jQuery("#customers-notifications").append(
            customerTpl.replace(/_id_customer_/g, parseInt(value.id_customer))
              .replace(/_customer_name_/g, value.customer_name)
              .replace(/_company_/g, (value.company !== "" ? " (" + value.company + ") " : ""))
              .replace(/_date_add_/g, value.date_add)
          );
        });

        jQuery.each(json.customer_message.results, function(property, value) {
          jQuery("#messages-notifications").append(
            messageTpl.replace(/_status_/g, value.status)
              .replace(/_customer_name_/g, value.customer_name)
              .replace(/_company_/g, (value.company !== "" ? " (" + value.company + ") " : ""))
              .replace(/_date_add_/g, value.date_add)
          );
        });

        setNotificationsNumber(jQuery('.notifications #orders-tab'), "_nb_new_orders_", nbOrders);
        setNotificationsNumber(jQuery('.notifications #customers-tab'), "_nb_new_customers_", nbCustomers);
        setNotificationsNumber(jQuery('.notifications #messages-tab'), "_nb_new_messages_", nbCustomerMessages);
        jQuery('#orders_notif_value').html(notifications_total);
      }
    }
  });

  let setNotificationsNumber = function (elt, id, number) {
    if (number > 0) {
      elt.html((elt.html().replace(id, " (" + number + ")")));
    } else {
      elt.html((elt.html().replace(id, "")));
    }
  }

}(window, $));
