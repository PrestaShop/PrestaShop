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

        let orders = '';
        let customers = '';
        let customerMessages = '';

        jQuery.each(json.order.results, function(property, value) {
          orders += `  <a href='#'>
                           #${parseInt(value.id_order)} - de ${value.customer_name} (${value.iso_code}) - ${value.carrier} ${value.total_paid}
                         </a>`;
        });

        jQuery.each(json.customer.results, function(property, value) {
          customers += `  <a href='#'>
                              #${parseInt(value.id_customer)} - ${value.customer_name} (${value.company}) - register ${value.date_add}
                            </a>`;
        });

        jQuery.each(json.customer_message.results, function(property, value) {
          customerMessages += `  <a href='#'>
                                      ${value.status} - ${value.customer_name} (${value.company}) - ${value.date_add}
                                   </a>`;
        });

        let notifications_total = parseInt(json.order.total) + parseInt(json.customer.total) + parseInt(json.customer_message.total);
        jQuery('#orders_notif_value').html(notifications_total);
        jQuery('#orders').html(orders);
        jQuery('#customers').html(customers);
        jQuery('#messages').html(customerMessages);
      }
    }
  });
}(window, $));
