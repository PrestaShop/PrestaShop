/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import refreshNotifications from '@js/notifications';
import ConfirmModal from '@components/modal';

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
    $(document).on('click', '.js-quick-link', (e) => {
      e.preventDefault();

      const $link = $(e.target).closest('.js-quick-link');
      const method = $link.data('method');

      if (method === 'add') {
        const $modal = $('#quick-access-add-modal');
        document.body.appendChild($modal[0]);
        const defaultName = (($link.data('link') as string) ?? '').substring(0, 32);

        $modal.find('#quick-access-name').val(defaultName);
        $modal.find('input[name="quick_access_new_window"][value="0"]').prop('checked', true);

        $modal.one('shown.bs.modal', () => {
          $modal.find('#quick-access-name').trigger('focus');
        });

        $modal.find('#quick-access-name').off('keypress').on('keypress', (keyEvent) => {
          if (keyEvent.key === 'Enter') {
            $modal.find('#quick-access-save-btn').trigger('click');
          }
        });

        const $nameInput = $modal.find('#quick-access-name');
        const $nameGroup = $modal.find('#quick-access-name-group');
        const $nameError = $modal.find('#quick-access-name-error');
        const $modalError = $modal.find('#quick-access-add-error');

        const resetErrors = (): void => {
          $nameGroup.removeClass('has-error');
          $nameError.removeClass('d-inline-block').addClass('d-none').find('.js-error-text').text('');
          $modalError.removeClass('alert alert-danger').addClass('d-none').find('.alert-text').text('');
        };

        $modal.one('hidden.bs.modal', resetErrors);
        $nameInput.off('input').on('input', resetErrors);

        $modal.find('#quick-access-save-btn').off('click').on('click', () => {
          resetErrors();

          const name = ($nameInput.val() as string).trim();

          if (!name) {
            $nameGroup.addClass('has-error');
            $nameError.removeClass('d-none').addClass('d-inline-block');
            $nameError.find('.js-error-text').text($nameInput.data('required-message') as string);
            $nameInput.trigger('focus');

            return;
          }

          const newWindow = $modal.find('input[name="quick_access_new_window"]:checked').val() === '1';
          this.doQuickLinkAction($link, method, name, newWindow, {
            onSuccess: () => $modal.modal('hide'),
            onError: (messages: string[]) => {
              $modalError.addClass('alert alert-danger').removeClass('d-none').find('.alert-text').text(messages.join(' '));
            },
          });
        });

        $modal.modal('show');

        return;
      }

      if (method === 'remove') {
        const confirmModal = new ConfirmModal(
          {
            id: 'quick-access-remove-confirm-modal',
            confirmTitle: $link.data('confirm-title'),
            confirmMessage: $link.data('confirm-message'),
            confirmButtonLabel: $link.data('confirm-button-label'),
            closeButtonLabel: $link.data('close-button-label'),
            confirmButtonClass: 'btn-danger',
          },
          () => this.doQuickLinkAction($link, method, null),
        );
        confirmModal.show();
      }
    });
  }

  private doQuickLinkAction(
    $link: JQuery,
    method: string,
    name: string | null,
    newWindow: boolean = false,
    callbacks: {onSuccess?: () => void; onError?: (messages: string[]) => void} = {},
  ): void {
    const postLink = $link.data('post-link');
    const quickLinkId = $link.data('quicklink-id');
    const url = $link.data('url');
    const icon = $link.data('icon');

    const reportErrors = (messages: string[]): void => {
      if (callbacks.onError) {
        callbacks.onError(messages);

        return;
      }

      messages.forEach((message) => {
        $.growl.error({
          title: '',
          message,
        });
      });
    };

    $.ajax({
      type: 'POST',
      headers: {
        'cache-control': 'no-cache',
      },
      async: true,
      url: postLink,
      data: {
        method,
        url,
        name,
        icon,
        id_quick_access: quickLinkId,
        new_window: newWindow ? 1 : 0,
      },
      dataType: 'json',
      success: (data) => {
        if (typeof data.has_errors !== 'undefined' && data.has_errors) {
          const messages: string[] = [];
          $.each(data, (index) => {
            if (typeof data[index] === 'string') {
              messages.push(data[index]);
            }
          });
          reportErrors(messages);
        } else if (Array.isArray(data)) {
          let quicklinkList = '';
          data.forEach((item) => {
            const classAttr = item.class ? ` ${item.class}` : '';
            const activeClass = item.active ? ' active' : '';
            const target = item.new_window ? ' target="_blank"' : '';
            quicklinkList += `<a class="dropdown-item quick-row-link${classAttr}${activeClass}"`
              + ` href="${item.link}"${target} data-item="${item.name}">${item.name}</a>`;
          });
          const $menu = $('#quick-access-container .dropdown-menu');
          $menu.find('.dropdown-divider').prevAll('a.quick-row-link').remove();
          $menu.prepend(quicklinkList);
          $link.remove();
          if (callbacks.onSuccess) {
            callbacks.onSuccess();
          }
          window.showSuccessMessage(window.update_success_msg);
        }
      },
      error: (xhr, textStatus) => {
        const message = textStatus === 'parsererror'
          ? `Server returned non-JSON (status ${xhr.status})`
          : `${xhr.status} ${xhr.statusText}`;

        if (callbacks.onError) {
          callbacks.onError([message]);

          return;
        }

        $.growl.error({
          title: 'Quick access error',
          message,
        });
      },
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
