$(document).ready(function() {
	var hints = $('.translatable span.hint');
	if (youEditFieldFor)
		hints.html(hints.html() + '<br /><span class="red">' + youEditFieldFor + '</span>');

	var html = "";
	var nb_notifs = 0;
	var wrapper_id = "";
	var type = new Array();

  $('.notification.dropdown-toggle').on('click', function (event) {
    $(this).parent().toggleClass('open');
    updateEmployeeNotifications();
  });

  $('body').on('click', function (e) {
    if (!$('#notification.dropdown').is(e.target)
      && $('#notification.dropdown').has(e.target).length === 0
      && $('.open').has(e.target).length === 0
    ) {
      if ($('#notification.dropdown').hasClass('open')) {
        getPush();
      }
      $('#notification.dropdown').removeClass('open');
    }
  });

  $('.notifications .nav-link').on('shown.bs.tab', function () {
    updateEmployeeNotifications();
  });

	// call it once immediately, then use setTimeout
	getPush();

});

function updateEmployeeNotifications() {
  $.post(
    baseAdminDir + "ajax.php",
    {
      "updateElementEmployee": "1",
      "updateElementEmployeeType": $('.notifications .nav-item.active a').attr('data-type')
    }
  );
}

function getPush()
{
	$.ajax({
		type: 'POST',
		headers: {"cache-control": "no-cache"},
		url: baseAdminDir+'ajax.php?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : 'json',
		data: {"getNotifications" : "1"},
		success: function(json) {
			if (json)
			{
				// Set moment language
				moment.lang(full_language_code);

        var nbOrders = parseInt(json.order.total);
        var nbCustomers = parseInt(json.customer.total);
        var nbCustomerMessages = parseInt(json.customer_message.total);
        var notifications_total = nbOrders + nbCustomers + nbCustomerMessages;

				// Add orders notifications to the list
				html = "";
				$.each(json.order.results, function(property, value) {
					html += "<a class='notif' href='"+baseAdminDir+"index.php?tab=AdminOrders&token=" + token_admin_orders + "&vieworder&id_order=" + parseInt(value.id_order) + "'>";
					html += "#" + parseInt(value.id_order) + " - ";
          html += from_msg + "&nbsp;<strong>" + value.customer_name + "</strong>";
          html += " (" + value.iso_code + ")";
					html += "<strong class='pull-right'>" + value.total_paid + "</strong>";
          if (value.carrier !== "") {
            html += " - " + value.carrier;
          }
					html += "</a>";
				});
        $("#orders-notifications").children('.notification-elements').empty();
				if (parseInt(json.order.total) > 0)
				{
          $("#orders-notifications").removeClass('empty');
					$("#orders-notifications").children('.notification-elements').append(html);
					$("#orders_notif_value").text(' (' + nbOrders + ')');
				} else {
          $("#orders-notifications").addClass('empty');
          $("#orders_notif_value").text('');
        }

				// Add customers notifications to the list
				html = "";
				$.each(json.customer.results, function(property, value) {
					html += "<a class='notif' href='"+baseAdminDir+"index.php?tab=AdminCustomers&token=" + token_admin_customers + "&viewcustomer&id_customer=" + parseInt(value.id_customer) + "'>";
					html += "#" + value.id_customer + " - <strong>" + value.customer_name + "</strong>"
          if (value.company !== "") {
            html += " (" + value.company + ")";
          }
          html += " - " + customer_name_msg + " " + value.date_add;
					html += "</a>";
				});
        $("#customers-notifications").children('.notification-elements').empty();
				if (parseInt(json.customer.total) > 0)
				{
          $("#customers-notifications").removeClass('empty');
          $("#customers-notifications").children('.notification-elements').append(html);
					$("#customers_notif_value").text(' (' + nbCustomers + ')');
				} else {
          $("#customers-notifications").addClass('empty');
          $("#customers_notif_value").text('');
        }

				// Add messages notifications to the list
				html = "";
				$.each(json.customer_message.results, function(property, value) {
					html += "<a class='notif' href='"+baseAdminDir+"index.php?tab=AdminCustomerThreads&token=" + token_admin_customer_threads + "&viewcustomer_thread&id_customer_thread=" + parseInt(value.id_customer_thread) + "'>";
					html += "<span class='message-notification-status " + value.status + "'><i class='material-icons'>fiber_manual_record</i> " + value.status + "</span> - ";
          html += "<strong>" + value.customer_name + "</strong>";
          if (value.company !== "") {
            html += " (" + value.company + ")";
          }
          html += " - <i class='material-icons'>access_time</i> " + value.date_add;
					html += "</a>";
				});
        $("#messages-notifications").children('.notification-elements').empty();
				if (parseInt(json.customer_message.total) > 0)
				{
          $("#messages-notifications").removeClass('empty');
          $("#messages-notifications").children('.notification-elements').append(html);
					$("#customer_messages_notif_value").text(' (' + nbCustomerMessages + ')');
				} else {
          $("#messages-notifications").addClass('empty');
          $("#customer_messages_notif_value").text('');
        }


        if (notifications_total > 0) {
          $("#total_notif_number_wrapper").removeClass('hide');
          $('#total_notif_value').text(notifications_total);
        } else {
          $("#total_notif_number_wrapper").addClass('hide');
        }
			}
      setTimeout("getPush()", 120000);
		}
	});
}
