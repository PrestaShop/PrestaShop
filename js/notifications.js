$(document).ready(function()
{
	var hints = $('.translatable span.hint');
	if (youEditFieldFor)
	{
		hints.html(hints.html() + '<br /><span class="red">' + youEditFieldFor + '</span>');
	}
	var html = "";		
	var nb_notifs = 0;
	var wrapper_id = "";
	var type = new Array();
	
	$(".notifs").live("click", function(){
		// Add class "open_notifs" to the clicked notification, remove the class from other notificationqs
		$('.notifs').removeClass('open_notifs');
		$(this).addClass('open_notifs');
		
		wrapper_id = $(this).attr("id");
		type = wrapper_id.split("s_notif")
		$.post("ajax.php",
			{
				"updateElementEmployee" : "1", "updateElementEmployeeType" : type[0]
			}, function(data) {
			if(data)
			{
				if(!$("#" + wrapper_id + "_wrapper").is(":visible"))
				{
					$(".notifs_wrapper").hide();
					$("#" + wrapper_id + "_number_wrapper").hide();  
					$("#" + wrapper_id + "_wrapper").show();  
				}else
				{
					$("#" + wrapper_id + "_wrapper").hide();							
				}
			}				
		});
	});
	
	$("#main").click(function(){
		$(".notifs_wrapper").hide();
		$('.notifs').removeClass('open_notifs');
	});

	// call it once immediately, then use setTimeout if refresh is activated
	getPush(autorefresh_notifications);
});

function getPush(refresh)
{
	$.post("ajax.php",{"getNotifications" : "1"}, function(data) {
		if (data)
		{
			var json = jQuery.parseJSON(data);

			// Add orders notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.order, function(property, value) {
				html += "<a href='index.php?tab=AdminOrders&token=" + token_admin_orders + "&vieworder&id_order=" + parseInt(value.id_order) + "' class='media list-group-item no_notifs'>";
				html += "<span class='pull-left'><i class='icon-time'></i></span>";
				html += "<span class='media-body'>";
				//html += "<p>" + new_order_msg + "</p>";
				html += "<p>" + order_number_msg + "&nbsp;<strong>#" + parseInt(value.id_order) + "</strong></p>";
				html += "<p class='pull-right'>" + total_msg + "&nbsp;<span class='label label-success'>" + value.total_paid + "</span></p>";
				html += "<p>" + from_msg + "&nbsp;<strong>" + value.customer_name + "</strong></p>";
				//html += "<p>" + see_order_msg + "</p>";
				html += "<small class='text-muted'>1 minute ago</small>";
				html += "</span></a>";
			});
			if (html != "")
			{
				//$("#list_orders_notif").prev("p").hide();
				$("#list_orders_notif").empty().append(html);
				nb_notifs = $("#list_orders_notif").length;
				$("#orders_notif_value").text(nb_notifs);
				$("#orders_notif_number_wrapper").show();
			}
			else
			{
				$("#orders_notif_number_wrapper").hide();
			}
			
			// Add customers notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.customer, function(property, value) {
				html += "<a href='index.php?tab=AdminCustomers&token=" + token_admin_customers + "&viewcustomer&id_customer=" + parseInt(value.id_customer) + "' class='media list-group-item no_notifs'>";
				html += "<span class='pull-left'><i class='icon-time'></i></span>";
				html += "<span class='media-body'>";
				//html += "<p>" + new_customer_msg + "</p>";
				html += "<p>" + customer_name_msg + "&nbsp;<strong>#" + value.customer_name + "</strong></p>";
				//html += "<p>" + see_customer_msg + "</p>";
				html += "<small class='text-muted'>1 minute ago</small>";
				html += "</span></a>";
			});						
			if (html != "")
			{
				//$("#list_customers_notif").prev("p").hide();
				$("#list_customers_notif").empty().append(html);
				nb_notifs = $("#list_customers_notif").length;
				$("#customers_notif_value").text(nb_notifs);
				$("#customers_notif_number_wrapper").show();
			}
			else
			{
				$("#customers_notif_number_wrapper").hide();
			}

			// Add messages notifications to the list
			html = "";
			nb_notifs = 0;
			$.each(json.customer_message, function(property, value) {
				html += "<a href='index.php?tab=AdminCustomerThreads&token=" + token_admin_customer_threads + "&viewcustomer_thread&id_customer_thread=" + parseInt(value.id_customer_thread) + "'>";
				html += "<span class='pull-left'><i class='icon-time'></i></span>";
				html += "<span class='media-body'>";
				//html += "<p>" + new_msg + "</p>";
				html += "<p>" + from_msg + "&nbsp;<strong>" + value.customer_name + "</strong></p>";
				//html += "<p>" + see_msg + "</p>";
				html += "<small class='text-muted'>1 minute ago</small>";
				html += "</span></a>";
			});

			if (html != "")
			{
				$("#list_customer_messages_notif").prev("p").hide();
				$("#list_customer_messages_notif").empty().append(html);
				nb_notifs = $("#list_customer_messages_notif li").length;
				$("#customer_messages_notif_value").text(nb_notifs);
				$("#customer_messages_notif_number_wrapper").show();
			}
			else
			{
				$("#customer_messages_notif_number_wrapper").hide();
			}
		}
		if(refresh)
			setTimeout("getPush(1)",60000);
	});
}