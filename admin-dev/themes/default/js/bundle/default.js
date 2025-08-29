/**
 * Default layout instanciation
 */
$(function () {
  const $this = $(this);
  const $ajaxSpinner = $('.ajax-spinner');
  $('[data-toggle="tooltip"]').tooltip();
  rightSidebar.init();
  /** spinner loading */
  $this.ajaxStart(() => {
    $ajaxSpinner.show();
  });
  $this.ajaxStop(() => {
    $ajaxSpinner.hide();
  });
  $this.ajaxError(() => {
    $ajaxSpinner.hide();
  });
});

const rightSidebar = (function () {
  return {
    init() {
      $('.btn-sidebar').on('click', function initLoadQuickNav() {
        $('div.right-sidebar-flex').removeClass('col-lg-12').addClass('col-lg-9');

        /** Lazy load of sidebar */
        const url = $(this).data('url');
        const target = $(this).data('target');

        if (url) {
          rightSidebar.loadQuickNav(url, target);
        }
      });
      $(document).on('hide.bs.sidebar', () => {
        $('div.right-sidebar-flex').removeClass('col-lg-9').addClass('col-lg-12');
      });
    },
    loadQuickNav(url, target) {
      /** Loads inner HTML in the sidebar container */
      $(target).load(url, function () {
        $(this).removeAttr('data-url');
        $('ul.pagination > li > a[href]', this).on('click', (e) => {
          e.preventDefault();
          rightSidebar.navigationChange($(e.target).attr('href'), $(target));
        });
        $('ul.pagination > li > input[name="paginator_jump_page"]', this).on('keyup', function (e) {
          if (e.which === 13) { // ENTER
            e.preventDefault();
            const val = parseInt($(e.target).val(), 10);
            const limit = $(e.target).attr('pslimit');
            const newUrl = $(this).attr('psurl').replace(/999999/, (val - 1) * limit);
            rightSidebar.navigationChange(newUrl, $(target));
          }
        });
      });
    },
    navigationChange(url, sidebar) {
      rightSidebar.loadQuickNav(url, sidebar);
    },
  };
}());

/**
 *  BO Events Handler
 */
// eslint-disable-next-line
window.BOEvent = {
  on(eventName, callback, context) {
    document.addEventListener(eventName, (event) => {
      if (typeof context !== 'undefined') {
        callback.call(context, event);
      } else {
        callback(event);
      }
    });
  },

  emitEvent(eventName, eventType) {
    const event = document.createEvent(eventType);
    // true values stand for: can bubble, and is cancellable
    event.initEvent(eventName, true, true);
    document.dispatchEvent(event);
  },
};
