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

        jQuery.each(json.order.results, function(property, value) {
          jQuery("#orders-notifications").append(
            orderTpl.replace("id_order", parseInt(value.id_order))
              .replace("customer_name", value.customer_name)
              .replace("iso_code", value.iso_code)
              .replace("carrier", value.carrier)
              .replace("total_paid", value.total_paid)
          );
        });

        jQuery.each(json.customer.results, function(property, value) {
          jQuery("#customers-notifications").append(
            customerTpl.replace("id_customer", parseInt(value.id_customer))
              .replace("customer_name", value.customer_name)
              .replace("company", value.company)
              .replace("date_add", value.date_add)
          );
        });

        jQuery.each(json.customer_message.results, function(property, value) {
          jQuery("#messages-notifications").append(
            messageTpl.replace("status", value.status)
              .replace("customer_name", value.customer_name)
              .replace("company", value.company)
              .replace("date_add", value.date_add)
          );
        });

        let notifications_total = parseInt(json.order.total) + parseInt(json.customer.total) + parseInt(json.customer_message.total);
        jQuery('#orders_notif_value').html(notifications_total);
      }
    }
  });
}(window, $));
