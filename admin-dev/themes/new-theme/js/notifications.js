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
            orderTpl.replace("_id_order_", parseInt(value.id_order))
              .replace("_customer_name_", value.customer_name)
              .replace("_iso_code_", value.iso_code)
              .replace("_carrier_", value.carrier)
              .replace("_total_paid_", value.total_paid)
          );
        });

        jQuery.each(json.customer.results, function(property, value) {
          jQuery("#customers-notifications").append(
            customerTpl.replace("_id_customer_", parseInt(value.id_customer))
              .replace("_customer_name_", value.customer_name)
              .replace("_company_", value.company)
              .replace("_date_add_", value.date_add)
          );
        });

        jQuery.each(json.customer_message.results, function(property, value) {
          jQuery("#messages-notifications").append(
            messageTpl.replace("_status_", value.status)
              .replace("_customer_name_", value.customer_name)
              .replace("_company_", value.company)
              .replace("_date_add_", value.date_add)
          );
        });

        let notifications_total = parseInt(json.order.total) + parseInt(json.customer.total) + parseInt(json.customer_message.total);
        jQuery('#orders_notif_value').html(notifications_total);
      }
    }
  });
}(window, $));
