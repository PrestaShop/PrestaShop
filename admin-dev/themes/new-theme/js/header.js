/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import refreshNotifications from './notifications.js';

export default class Header {
  constructor() {
    $(() => {
      this.initQuickAccess();
      this.initMultiStores();
      this.initNotificationsToggle();
      this.initSearch();
      refreshNotifications();
    });
  }

  initQuickAccess() {
    $('.js-quick-link').on('click', (e) => {
      e.preventDefault();

      let method = $(e.target).data('method');
      let name = null;

      if (method === 'add') {
        let text = $(e.target).data('prompt-text');
        let link = $(e.target).data('link');

        name = prompt(text, link);
      }
      if (method === 'add' && name || method === 'remove') {
        let postLink = $(e.target).data('post-link');
        let quickLinkId = $(e.target).data('quicklink-id');
        let rand = $(e.target).data('rand');
        let url = $(e.target).data('url');
        let icon = $(e.target).data('icon');

        $.ajax({
          type: 'POST',
          headers: {
            "cache-control": "no-cache"
          },
          async: true,
          url: `${postLink}&action=GetUrl&rand=${rand}&ajax=1&method=${method}&id_quick_access=${quickLinkId}`,
          data: {
            "url": url,
            "name": name,
            "icon": icon
          },
          dataType: "json",
          success: (data) => {
            var quicklink_list = '';
            $.each(data, (index) => {
              if (typeof data[index]['name'] !== 'undefined')
                quicklink_list += '<li><a href="' + data[index]['link'] + '&token=' + data[index]['token'] + '"><i class="icon-chevron-right"></i> ' + data[index]['name'] + '</a></li>';
            });

            if (typeof data['has_errors'] !== 'undefined' && data['has_errors'])
              $.each(data, (index) => {
                if (typeof data[index] === 'string')
                  $.growl.error({
                    title: '',
                    message: data[index]
                  });
              });
            else if (quicklink_list) {
              $("#header_quick ul.dropdown-menu").html(quicklink_list);
              window.showSuccessMessage(window.update_success_msg);
            }
          }
        });
      }
    });
  }

  initMultiStores() {
    $('.js-link').on('click', (e) => {
      window.open($(e.target).parents('.link').attr('href'), '_blank');
    });
  }

  initNotificationsToggle() {
    $('#notif').on({
      "shown.bs.dropdown": function() { $(this).attr('closable', false); },
      'hide.bs.dropdown': function () { return $(this).attr('closable') == 'true';}
    });

    $('.notification.dropdown-toggle').on('click', () => {
      if(!$('.mobile-nav').hasClass('expanded')) {
        this.updateEmployeeNotifications();
      }

      $('#notif').attr('closable', true );
    });

    $('body').on('click', function (e) {
      if (!$('div.notification-center.dropdown').is(e.target)
        && $('div.notification-center.dropdown').has(e.target).length === 0
        && $('.open').has(e.target).length === 0
      ) {

        if ($('div.notification-center.dropdown').hasClass('open')) {
          $('.mobile-layer').removeClass('expanded');
          refreshNotifications();
        }

        $('#notif').attr('closable', true);
      }
    });

    $('.notification-center .nav-link').on('shown.bs.tab', () => {
      this.updateEmployeeNotifications();
    });
  }

  initSearch() {
    $('.js-items-list').on('click', (e) => {
      $('.js-form-search').attr('placeholder', $(e.target).data('placeholder'));
      $('.js-search-type').val($(e.target).data('value'));
      $('.js-dropdown-toggle').text($(e.target).data('item'));
    });
  }

  updateEmployeeNotifications() {
    $.post(
      baseAdminDir + "ajax.php",
      {
        "updateElementEmployee": "1",
        "updateElementEmployeeType": $('.notification-center .nav-link.active').attr('data-type')
      }
    );
  }
}
