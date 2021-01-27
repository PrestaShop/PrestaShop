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
import refreshNotifications from '@js/notifications.js';

const {$} = window;

export default class Header {
  constructor() {
    $(() => {
      this.initQuickAccess();
      this.initMultiStores();
      this.initNotificationsToggle();
      this.initSearch();
      this.initContentDivOffset();
      refreshNotifications();
    });
  }

  initQuickAccess() {
    $('.js-quick-link').on('click', (e) => {
      e.preventDefault();

      const method = $(e.target).data('method');
      let name = null;

      if (method === 'add') {
        const text = $(e.target).data('prompt-text');
        const link = $(e.target).data('link');

        name = prompt(text, link);
      }

      if ((method === 'add' && name) || method === 'remove') {
        const postLink = $(e.target).data('post-link');
        const quickLinkId = $(e.target).data('quicklink-id');
        const rand = $(e.target).data('rand');
        const url = $(e.target).data('url');
        const icon = $(e.target).data('icon');

        $.ajax({
          type: 'POST',
          headers: {
            'cache-control': 'no-cache',
          },
          async: true,
          url: `${postLink}&action=GetUrl&rand=${rand}&ajax=1&method=${method}&id_quick_access=${quickLinkId}`,
          data: {
            url,
            name,
            icon,
          },
          dataType: 'json',
          success: (data) => {
            let quicklinkList = '';
            $.each(data, (index) => {
              /* eslint-disable-next-line max-len */
              if (typeof data[index].name !== 'undefined') quicklinkList += `<li><a href="${data[index].link}&token=${data[index].token}"><i class="icon-chevron-right"></i> ${data[index].name}</a></li>`;
            });

            if (typeof data.has_errors !== 'undefined' && data.has_errors) {
              $.each(data, (index) => {
                if (typeof data[index] === 'string') {
                  $.growl.error({
                    title: '',
                    message: data[index],
                  });
                }
              });
            } else if (quicklinkList) {
              $('#header_quick ul.dropdown-menu .divider')
                .prevAll()
                .remove();
              $('#header_quick ul.dropdown-menu').prepend(quicklinkList);
              $(e.target).remove();
              window.showSuccessMessage(window.update_success_msg);
            }
          },
        });
      }
    });
  }

  initMultiStores() {
    $('.js-link').on('click', (e) => {
      window.open(
        $(e.target)
          .parents('.link')
          .attr('href'),
        '_blank',
      );
    });
  }

  initNotificationsToggle() {
    $('.notification.dropdown-toggle').on('click', () => {
      if (!$('.mobile-nav').hasClass('expanded')) {
        this.updateEmployeeNotifications();
      }
    });

    $('body').on('click', (e) => {
      if (
        !$('div.notification-center.dropdown').is(e.target)
        && $('div.notification-center.dropdown').has(e.target).length === 0
        && $('.open').has(e.target).length === 0
      ) {
        if ($('div.notification-center.dropdown').hasClass('open')) {
          $('.mobile-layer').removeClass('expanded');
          refreshNotifications();
        }
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
    $.post(window.adminNotificationPushLink, {
      type: $('.notification-center .nav-link.active').attr('data-type'),
    });
  }

  /**
   * Updates the offset of the content div in whenever the header changes size
   */
  initContentDivOffset() {
    const onToolbarResize = function () {
      const toolbar = $('.header-toolbar').last();
      const header = $('.main-header');
      const content = $('.content-div');
      const spacing = 15;

      if (toolbar.length && header.length && content.length) {
        content.css('padding-top', toolbar.outerHeight() + header.outerHeight() + spacing);
      }
    };

    // update the offset now
    onToolbarResize();

    // update when resizing the window
    $(window).resize(onToolbarResize);

    // update when replacing the header with a vue header
    $(document).on('vueHeaderMounted', onToolbarResize);
  }
}
