/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
$(document).ready(function () {
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
  const query = `tab=AdminOrders&token=${token_admin_orders}&vieworder&id_order=${value.id_order}`;
  const carrier = value.carrier !== '' ? ` - ${value.carrier}` : '';
  return `
    <a class="notif" href="${baseAdminDir}index.php?${query}">
      #${value.id_order} - ${from_msg}&nbsp;<strong>${value.customer_name}</strong> (${value.iso_code})
      <strong class="pull-right">${value.total_paid}</strong>${carrier}
    </a>
  `;
}

function renderCustomerNotification(value) {
  const company = value.company !== '' ? ` (${value.company})` : '';
  return `
    <a class="notif" href="${value.customer_view_url}">
      #${value.id_customer} - <strong>${value.customer_name}</strong>${company} - ${customer_name_msg} ${value.date_add};
    </a>
  `;
}

function renderMessageNotification(value) {
  const query = `tab=AdminCustomerThreads&token=${token_admin_customer_threads}&viewcustomer_thread&id_customer_thread=${value.id_customer_thread}`;
  const company = value.company !== '' ? ` (${value.company})` : '';
  return `
    <a class="notif" href="${baseAdminDir}index.php?${query}">
      <span class="message-notification-status ${value.status}">
        <i class="material-icons">fiber_manual_record</i> ${value.status}
      </span>
       - <strong>${value.customer_name}</strong> ${company}
       - <i class="material-icons">access_time</i> ${value.date_add}
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

      // Add orders notifications to the list
      renderNotifications('orders-notifications', json.order, renderOrderNotification);

      // Add customers notifications to the list
      renderNotifications('customers-notifications', json.customer, renderCustomerNotification);

      // Add messages notifications to the list
      renderNotifications('messages-notifications', json.customer_message, renderMessageNotification);

      var notifCount = parseInt(json.order.total) + parseInt(json.customer.total) + parseInt(json.customer_message.total);
      if (notifCount > 0) {
        $("#total_notif_number_wrapper").removeClass('hide');
        $('#total_notif_value').text(notifCount);
      } else {
        $("#total_notif_number_wrapper").addClass('hide');
      }
    }
  });
}
