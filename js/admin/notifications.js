/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
$(function () {
  if (youEditFieldFor) {
    $('.translatable span.hint').append(`<br /><span class="red">${youEditFieldFor}</span>`);
  }

  $('.notification.dropdown-toggle').on('click', function () {
    $(this).parent().toggleClass('open');
    updateEmployeeNotifications();
  });

  $(document).on('click', function (e) {
    if (!$(e.target).closest('#notification').length && $('#notification').hasClass('open')) {
      $('#notification').removeClass('open');
      getPush();
    }
  });

  $('.notifications .nav-link').on('shown.bs.tab', function () {
    updateEmployeeNotifications();
  });

  // call it once immediately, then use setTimeout
  if (parseInt(show_new_orders) || parseInt(show_new_customers) || parseInt(show_new_messages)) {
    getPush();
  }
});

function updateEmployeeNotifications() {
  $.post(
    admin_notification_push_link,
    {
      type: $('.notifications .nav-item.active a').data('type')
    }
  );
}

function renderOrderNotification(value) {
  const carrier = value.carrier !== '' ? ` - ${value.carrier}` : '';
  return `
    <a class="notif" href="${value.order_view_url}">
      <span class="notif__id">#${value.id_order}</span>
      <span class="notif__customer">
       - ${from_msg} <strong>${value.customer_name}</strong> <span class="notif__iso">(${value.iso_code})</span>
      </span>
      <span class="notif__order-info">
       <span class="notif__carrier">${carrier} -</span> <strong class="notif__total">${value.total_paid}</strong>
      </span>
    </a>
  `;
}

function renderCustomerNotification(value) {
  const company = value.company !== '' ? ` (${value.company})` : '';
  return `
    <a class="notif" href="${value.customer_view_url}">
      <span class="notif__id">#${value.id_customer}</span>
      <span class="notif__customer">
       - <strong>${value.customer_name}</strong> ${company} - 
      </span>
      <span class="notif__registered-date">${customer_name_msg} <strong>${value.date_add}</strong></span>
    </a>
  `;
}

function renderMessageNotification(value) {
  const company = value.company !== '' ? ` (${value.company})` : '';
  return `
    <a class="notif" href="${value.customer_thread_view_url}">
      <span class="notif__status ${value.status}">
        <i class="material-icons">fiber_manual_record</i> ${value.status}
      </span>
      <span class="notif__customer">
       - <strong>${value.customer_name}</strong> ${company} - 
      </span>
      <span class="notif__date">
        <i class="material-icons">access_time</i> ${value.date_add}
      </span>
    </a>
  `;
}

function renderNotifications(panelId, data, renderFn) {
  var panel = $('#' + panelId);
  var tabCounter = panel.closest('#notification').find(`a[href="#${panelId}"] .notif-counter`);
  if (data.total > 0) {
    var html = data.results.map(renderFn).join('')
    panel.removeClass('empty').children('.notification-elements').html(html);
    tabCounter.text(` (${data.total})`).data('nb', data.total);
  } else {
    panel.addClass('empty').children('.notification-elements').empty();
    tabCounter.text('');
  }
}

function getPush() {
  $.ajax({
    type: 'POST',
    headers: { "cache-control": "no-cache" },
    url: `${admin_notification_get_link}&rand=${new Date().getTime()}`,
    cache: false,
    dataType: 'json',
    success: function (json) {
      setTimeout(getPush, 120000);
      if (!json) {
        return;
      }

      var notifCount = 0;

      if (json.hasOwnProperty('order')) {
        // Add orders notifications to the list
        renderNotifications('orders-notifications', json.order, renderOrderNotification);
        notifCount += parseInt(json.order.total)
      }

      if (json.hasOwnProperty('customer')) {
        // Add customers notifications to the list
        renderNotifications('customers-notifications', json.customer, renderCustomerNotification);
        notifCount += parseInt(json.customer.total)
      }

      if (json.hasOwnProperty('customer_message')) {
        // Add messages notifications to the list
        renderNotifications('messages-notifications', json.customer_message, renderMessageNotification);
        notifCount += parseInt(json.customer_message.total)
      }

      if (notifCount > 0) {
        $("#total_notif_number_wrapper").removeClass('hide');
        $('#total_notif_value').text(notifCount);
      } else {
        $("#total_notif_number_wrapper").addClass('hide');
      }
    }
  });
}
