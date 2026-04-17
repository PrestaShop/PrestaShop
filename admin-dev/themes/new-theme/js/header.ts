/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import refreshNotifications from '@js/notifications';

const {$} = window;

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

  initQuickAccess(): void {
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

        const postUrl = new URL(postLink, window.location.origin);
        postUrl.searchParams.append('action', 'GetUrl');
        postUrl.searchParams.append('rand', String(rand));
        postUrl.searchParams.append('ajax', '1');
        postUrl.searchParams.append('method', String(method));
        postUrl.searchParams.append('id_quick_access', String(quickLinkId));

        $.ajax({
          type: 'POST',
          headers: {
            'cache-control': 'no-cache',
          },
          async: true,
          url: postUrl.toString(),
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

  initMultiStores(): void {
    $('.js-link').on('click', (e) => {
      window.open(
        $(e.target)
          .parents('.link')
          .attr('href'),
        '_blank',
      );
    });
  }

  initNotificationsToggle(): void {
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

  initSearch(): void {
    $('.js-items-list').on('click', (e) => {
      $('.js-form-search').attr('placeholder', $(e.target).data('placeholder'));
      $('.js-search-type').val($(e.target).data('value'));
      $('.js-dropdown-toggle').text($(e.target).data('item'));
    });
  }

  updateEmployeeNotifications(): void {
    $.post(window.adminNotificationPushLink, {
      type: $('.notification-center .nav-link.active').attr('data-type'),
    });
  }
}
