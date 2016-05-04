const refreshNotifications = function () {
  let timer = null;

  $.ajax({
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

        fillTpl(json.order.results, $("#orders-notifications"), $("#order-notification-template").html());
        fillTpl(json.customer.results, $("#customers-notifications"), $("#customer-notification-template").html());
        fillTpl(json.customer_message.results, $("#messages-notifications"), $("#message-notification-template").html());

        setNotificationsNumber($('.notifications #orders-tab'), "_nb_new_orders_", nbOrders);
        setNotificationsNumber($('.notifications #customers-tab'), "_nb_new_customers_", nbCustomers);
        setNotificationsNumber($('.notifications #messages-tab'), "_nb_new_messages_", nbCustomerMessages);
        $('#orders_notif_value').html(notifications_total);
      }
      timer = setTimeout(refreshNotifications, 120000);
    }
  });

  clearTimeout(timer);
}

let fillTpl = function (results, eltAppendTo, tpl) {
  eltAppendTo.empty();
  $.each(results, function(property, value) {
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
        .replace(/order_url/g, `${baseAdminDir}index.php?tab=AdminOrders&token=${token_admin_orders}&vieworder&id_order=${value.id_order}`)
        .replace(/customer_url/g, `${baseAdminDir}index.php?tab=AdminCustomers&token=${token_admin_customers}&viewcustomer&id_customer=${value.id_customer}`)
        .replace(/message_url/g, `${baseAdminDir}index.php?tab=AdminCustomerThreads&token=${token_admin_customer_threads}&viewcustomer_thread&id_customer_thread=${value.id_customer_thread}`)
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

export default refreshNotifications
