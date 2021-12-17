/* ========================================================================
 * Bootstrap: sidebar.js v0.1
 * ========================================================================
 * Copyright 2011-2014 Asyraf Abdul Rahman
 * Licensed under MIT
 * ======================================================================== */

(function ($) {
  // SIDEBAR PUBLIC CLASS DEFINITION
  // ================================

  const Sidebar = function (element, options) {
    this.$element = $(element);
    this.options = $.extend({}, Sidebar.DEFAULTS, options);
    this.transitioning = null;

    if (this.options.parent) {
      this.$parent = $(this.options.parent);
    }
    if (this.options.toggle) {
      this.toggle();
    }
  };

  Sidebar.DEFAULTS = {
    toggle: true,
  };

  Sidebar.prototype.show = function () {
    if (this.transitioning || this.$element.hasClass('sidebar-open')) {
      return;
    }

    const startEvent = $.Event('show.bs.sidebar');
    this.$element.trigger(startEvent);
    if (startEvent.isDefaultPrevented()) {
      return;
    }

    this.$element
      .addClass('sidebar-open');

    this.transitioning = 1;

    const complete = function () {
      this.transitioning = 0;
      this.$element.trigger('shown.bs.sidebar');
    };

    if (!$.support.transition) {
      // eslint-disable-next-line
      return complete.call(this);
    }

    this.$element
      .one($.support.transition.end, $.proxy(complete, this))
      .emulateTransitionEnd(400);
  };

  Sidebar.prototype.hide = function () {
    if (this.transitioning || !this.$element.hasClass('sidebar-open')) {
      return;
    }

    const startEvent = $.Event('hide.bs.sidebar');
    this.$element.trigger(startEvent);
    if (startEvent.isDefaultPrevented()) {
      return;
    }

    this.$element
      .removeClass('sidebar-open');

    this.transitioning = 1;

    const complete = function () {
      this.transitioning = 0;
      this.$element
        .trigger('hidden.bs.sidebar');
    };

    if (!$.support.transition) {
      // eslint-disable-next-line
      return complete.call(this);
    }

    this.$element
      .one($.support.transition.end, $.proxy(complete, this))
      .emulateTransitionEnd(400);
  };

  Sidebar.prototype.toggle = function () {
    this[this.$element.hasClass('sidebar-open') ? 'hide' : 'show']();
  };

  const old = $.fn.sidebar;

  $.fn.sidebar = function (option) {
    return this.each(function () {
      const $this = $(this);
      let data = $this.data('bs.sidebar');
      const options = $.extend({}, Sidebar.DEFAULTS, $this.data(), typeof this.options === 'object' && option);

      if (!data && options.toggle && option === 'show') {
        // eslint-disable-next-line
        option = !option;
      }
      if (!data) {
        $this.data('bs.sidebar', (data = new Sidebar(this, options)));
      }
      if (typeof option === 'string') {
        data[option]();
      }
    });
  };

  $.fn.sidebar.Constructor = Sidebar;

  $.fn.sidebar.noConflict = function () {
    $.fn.sidebar = old;
    return this;
  };

  $(document).on('click.bs.sidebar.data-api', '[data-toggle="sidebar"]', function (e) {
    const $this = $(this);
    let href;
    // eslint-disable-next-line
    const target = $this.attr('data-target') || e.preventDefault() || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '');
    const $target = $(target);
    const data = $target.data('bs.sidebar');
    const option = data ? 'toggle' : $this.data();

    $target.sidebar(option);
  });

  $('html').on('click.bs.sidebar.autohide', (event) => {
    const $this = $(event.target);
    /* eslint-disable */
    const isButtonOrSidebar = $this.is('.sidebar, [data-toggle="sidebar"]') || $this.parents('.sidebar, [data-toggle="sidebar"]').length;

    if (!isButtonOrSidebar) {
      const $target = $('.sidebar');
      $target.each((i, trgt) => {
        const $trgt = $(trgt);

        if ($trgt.data('bs.sidebar') && $trgt.hasClass('sidebar-open')) {
          $trgt.sidebar('hide');
        }
      });
    }
  });
}(jQuery));
