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

// build confirmation modal
// eslint-disable-next-line
function confirm_modal(
  heading,
  question,
  leftButtonText,
  rightButtonText,
  leftButtonCallback,
  rightButtonCallback,
) {
  const confirmModal = $(
    `${'<div class="bootstrap modal hide fade">'
      + '<div class="modal-dialog">'
      + '<div class="modal-content">'
      + '<div class="modal-header">'
      + '<a class="close" data-dismiss="modal" >&times;</a>'
      + '<h3>'}${heading}</h3>`
      + '</div>'
      + '<div class="modal-body">'
      + `<p>${question}</p>`
      + '</div>'
      + '<div class="modal-footer">'
      + `<a href="#" id="confirm-modal-left-button" class="btn btn-primary">${leftButtonText}</a>`
      + `<a href="#" id="confirm-modal-right-button" class="btn btn-primary">${rightButtonText}</a>`
      + '</div>'
      + '</div>'
      + '</div>'
      + '</div>',
  );
  confirmModal.find('#confirm-modal-left-button').on('click', () => {
    leftButtonCallback();
    confirmModal.modal('hide');
  });
  confirmModal.find('#confirm-modal-right-button').on('click', () => {
    rightButtonCallback();
    confirmModal.modal('hide');
  });
  confirmModal.modal('show');
}

// build error modal
/* global errorContinueMsg */
// eslint-disable-next-line
function error_modal(heading, msg) {
  const errorModal = $(
    `${'<div class="bootstrap modal hide fade">'
      + '<div class="modal-dialog">'
      + '<div class="modal-content">'
      + '<div class="modal-header">'
      + '<a class="close" data-dismiss="modal" >&times;</a>'
      + '<h4>'}${heading}</h4>`
      + '</div>'
      + '<div class="modal-body">'
      + `<p>${msg}</p>`
      + '</div>'
      + '<div class="modal-footer">'
      + `<a href="#" id="error_modal_right_button" class="btn btn-default">${errorContinueMsg}</a>`
      + '</div>'
      + '</div>'
      + '</div>'
      + '</div>',
  );
  errorModal.find('#error_modal_right_button').on('click', () => {
    errorModal.modal('hide');
  });
  errorModal.modal('show');
}

// move to hash after clicking on anchored links
// eslint-disable-next-line
function scroll_if_anchor(href) {
  // eslint-disable-next-line
  href = typeof href === 'string' ? href : $(this).attr('href');
  const fromTop = 120;

  if (href.indexOf('#') === 0) {
    const $target = $(href);

    if ($target.length) {
      $('html, body').animate({scrollTop: $target.offset().top - fromTop});
      if (history && 'pushState' in history) {
        history.pushState({}, document.title, window.location.href + href);
        return false;
      }
    }
  }
}
$(() => {
  const $mainMenu = $('.main-menu');
  const $navBar = $('.nav-bar');
  const $body = $('body');

  const NavBarTransitions = new NavbarTransitionHandler(
    $navBar,
    $mainMenu,
    getAnimationEvent('transition', 'end'),
    $body,
  );

  $('.nav-bar-overflow').on('scroll', () => {
    const $menuItems = $('.main-menu .link-levelone.has_submenu.ul-open');

    $($menuItems).each((i, e) => {
      const itemOffsetTop = $(e).position().top;
      $(e)
        .find('ul.submenu')
        .css('top', itemOffsetTop);
    });
  });

  $('.nav-bar')
    .find('.link-levelone')
    .on(
      'mouseenter',
      function () {
        $(this).addClass('-hover');
      },
    ).on(
      'mouseleave',
      function () {
        $(this).removeClass('-hover');
      },
    );

  $('.nav-bar li.link-levelone.has_submenu > a').on('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    NavBarTransitions.toggle();

    const $submenu = $(this).parent();
    $('.nav-bar li.link-levelone.has_submenu a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
    const onlyClose = $(e.currentTarget)
      .parent()
      .hasClass('ul-open');

    if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
      $('.nav-bar li.link-levelone.has_submenu.ul-open').removeClass('ul-open open -hover');
      $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').removeAttr('style');
    } else {
      $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').slideUp({
        complete() {
          $(this)
            .parent()
            .removeClass('ul-open open');
          $(this).removeAttr('style');
        },
      });
    }

    if (onlyClose) {
      return;
    }
    $submenu.addClass('ul-open');

    if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
      $submenu.addClass('-hover');
      $submenu.find('ul.submenu').removeAttr('style');
    } else {
      $submenu.find('ul.submenu').slideDown({
        complete() {
          $submenu.addClass('open');
          $(this).removeAttr('style');
        },
      });
    }
    $submenu.find('i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');

    const itemOffsetTop = $submenu.position().top;
    $submenu.find('.submenu').css('top', itemOffsetTop);
  });

  $('.nav-bar').on('click', '.menu-collapse', function () {
    $('body').toggleClass('page-sidebar-closed');
    $('.main-menu').toggleClass('sidebar-closed');

    if ($('body').hasClass('page-sidebar-closed')) {
      $('nav.nav-bar ul.main-menu > li')
        .removeClass('ul-open open')
        .find('a > i.material-icons.sub-tabs-arrow')
        .text('keyboard_arrow_down');
      addMobileBodyClickListener();
    } else {
      $('nav.nav-bar ul.main-menu > li.-active')
        .addClass('ul-open open')
        .find('a > i.material-icons.sub-tabs-arrow')
        .text('keyboard_arrow_up');
      $('body').off('click.mobile');
    }

    $.ajax({
      url: $(this).data('toggle-url'),
      type: 'post',
      cache: false,
      data: {
        shouldCollapse: Number($('body').hasClass('page-sidebar-closed')),
      },
    });
  });
  addMobileBodyClickListener();
  const MAX_MOBILE_WIDTH = 1023;

  if ($(window).width() <= MAX_MOBILE_WIDTH) {
    mobileNav();
  }

  $(window).on('resize', () => {
    if ($('body').hasClass('mobile') && $(window).width() > MAX_MOBILE_WIDTH) {
      unbuildMobileMenu();
    } else if (!$('body').hasClass('mobile') && $(window).width() <= MAX_MOBILE_WIDTH) {
      mobileNav();
      $('nav.nav-bar ul.main-menu').removeClass('sidebar-closed');
    }
  });

  function addMobileBodyClickListener() {
    if (!$('body').is('.page-sidebar-closed:not(.mobile)')) {
      return;
    }
    // To close submenu on mobile devices
    $('body').on('click.mobile', () => {
      if ($('ul.main-menu li.ul-open').length > 0) {
        $('.nav-bar li.link-levelone.has_submenu.ul-open').removeClass('ul-open open -hover');
        $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').removeAttr('style');
      }
    });
  }

  function mobileNav() {
    const $logout = $('#header_logout')
      .addClass('link')
      .removeClass('m-t-1')
      .prop('outerHTML');
    let $employee = $('.employee_avatar');

    // Legacy
    if ($('#employee_links').length > 0) {
      $employee = $('<a class="employee_avatar">').attr('href', $('#employee_infos > a.employee_name').attr('href'));
      $employee.append($('#employee_links .employee_avatar').clone());
      $employee.append($('<span></span>').append($('#employee_links > .username').text()));
    }
    const profileLink = $('.profile-link').attr('href');

    $('.nav-bar li.link-levelone.has_submenu:not(.open) a > i.material-icons.sub-tabs-arrow').text(
      'keyboard_arrow_down',
    );
    $('body').addClass('mobile');
    $('.nav-bar')
      .addClass('mobile-nav')
      .attr('style', 'margin-left: -100%;');
    $('.panel-collapse').addClass('collapse');
    $('.link-levelone a').each((index, el) => {
      const id = $(el)
        .parent()
        .find('.collapse')
        .attr('id');

      if (id) {
        $(el)
          .attr('href', `#${id}`)
          .attr('data-toggle', 'collapse');
      }
    });
    $('.main-menu').append(`<li class="link-levelone" data-submenu="">${$logout}</li>`);
    $('.main-menu').prepend(`<li class="link-levelone">${$employee.prop('outerHTML')}</li>`);
    $('.collapse').collapse({
      toggle: false,
    });
    if ($('#employee_links').length === 0) {
      $('.employee_avatar .material-icons, .employee_avatar span').wrap(`<a href="${profileLink}"></a>`);
    }
    $('.js-mobile-menu').on('click', expand);
    $('.js-notifs_dropdown').css({
      height: window.innerHeight,
    });

    function expand() {
      if ($('div.notification-center.dropdown').hasClass('open')) {
        return;
      }

      if ($('.mobile-nav').hasClass('expanded')) {
        $('.mobile-nav').animate(
          {'margin-left': '-100%'},
          {
            complete() {
              $('.nav-bar, .mobile-layer').removeClass('expanded');
              $('.nav-bar, .mobile-layer').addClass('d-none');
            },
          },
        );
        $('.mobile-layer').off();
      } else {
        $('.nav-bar, .mobile-layer').addClass('expanded');
        $('.nav-bar, .mobile-layer').removeClass('d-none');
        $('.mobile-layer').on('click', expand);
        $('.mobile-nav').animate({'margin-left': 0});
      }
    }
  }

  function unbuildMobileMenu() {
    $('body').removeClass('mobile');
    $('body.page-sidebar-closed .nav-bar .link-levelone.open').removeClass('ul-open open');
    $('.main-menu li:first, .main-menu li:last').remove();
    $('.js-notifs_dropdown').removeAttr('style');
    $('.nav-bar')
      .removeClass('mobile-nav expanded')
      .addClass('d-none')
      .css('margin-left', 0);
    $('.js-mobile-menu').off();
    $('.panel-collapse')
      .removeClass('collapse')
      .addClass('submenu');
    $('.shop-list-title').remove();
    $('.mobile-layer')
      .addClass('d-none')
      .removeClass('expanded');
  }

  // scroll top
  function animateGoTop() {
    if ($(window).scrollTop()) {
      $('#go-top:hidden')
        .stop(true, true)
        .fadeIn();
      $('#go-top:hidden').removeClass('hide');
    } else {
      $('#go-top')
        .stop(true, true)
        .fadeOut();
    }
  }

  // media queries - depends of enquire.js
  /* global enquire */
  enquire.register('screen and (max-width: 1200px)', {
    match() {
      if ($('#main').hasClass('helpOpen')) {
        $('.toolbarBox a.btn-help').trigger('click');
      }
    },
    unmatch() {},
  });

  // bootstrap components init
  $('.dropdown-toggle').dropdown();
  $('.label-tooltip, .help-tooltip').tooltip();
  $('#error-modal').modal('show');

  // go on top of the page
  $('#go-top').on('click', () => {
    $('html, body').animate({scrollTop: 0}, 'slow');
    return false;
  });

  let timer;
  $(window).on('scroll', () => {
    if (timer) {
      window.clearTimeout(timer);
    }
    timer = window.setTimeout(() => {
      animateGoTop();
    }, 100);
  });

  // search with nav sidebar closed
  $(document).on('click', '.page-sidebar-closed .searchtab', function () {
    $(this).addClass('search-expanded');
    $(this)
      .find('#bo_query')
      .focus();
  });

  $('.page-sidebar-closed').on('click', () => {
    $('.searchtab').removeClass('search-expanded');
  });

  $('#header_search button').on('click', (e) => {
    e.stopPropagation();
  });

  // erase button search input
  if ($('#bo_query').val() !== '') {
    $('.clear_search').removeClass('hide');
  }

  $('.clear_search').on('click', function (e) {
    e.stopPropagation();
    e.preventDefault();
    const id = $(this)
      .closest('form')
      .attr('id');
    $(`#${id} #bo_query`)
      .val('')
      .focus();
    $(`#${id} .clear_search`).addClass('hide');
  });
  $('#bo_query').on('keydown', () => {
    if ($('#bo_query').val() !== '') {
      $('.clear_search').removeClass('hide');
    }
  });

  // search with nav sidebar opened
  $('.page-sidebar').on('click', () => {
    $('#header_search .form-group').removeClass('focus-search');
  });

  // eslint-disable-next-line
  $('#header_search #bo_query').on('click', (e) => {
    e.stopPropagation();
    e.preventDefault();
    if ($('body').hasClass('mobile-nav')) {
      return false;
    }
    $('#header_search .form-group').addClass('focus-search');
  });

  // select list for search type
  $('#header_search_options').on('click', 'li a', function (e) {
    e.preventDefault();
    $('#header_search_options .search-option').removeClass('active');
    $(this)
      .closest('li')
      .addClass('active');
    $('#bo_search_type').val($(this).data('value'));
    $('#search_type_icon').text($(this).find('.material-icons').text());
    $('#bo_query').attr('placeholder', $(this).data('placeholder'));
    $('#bo_query').focus();
  });

  // scroll_if_anchor(window.location.hash);
  $('body').on('click', 'a.anchor', scroll_if_anchor);

  // manage curency status switcher
  $('#currencyStatus input').on('change', function () {
    const parentZone = $(this)
      .parent()
      .parent()
      .parent()
      .parent();
    parentZone.find('.status').addClass('hide');

    if ($(this).attr('checked') === 'checked') {
      parentZone.find('.enabled').removeClass('hide');
      $('#currency_form #active').val(1);
    } else {
      parentZone.find('.disabled').removeClass('hide');
      $('#currency_form #active').val(0);
    }
  });

  $('#currencyCronjobLiveExchangeRate input').on('change', function () {
    let enable = 0;
    const parentZone = $(this)
      .parent()
      .parent()
      .parent()
      .parent();
    parentZone.find('.status').addClass('hide');

    if ($(this).attr('checked') === 'checked') {
      enable = 1;
      parentZone.find('.enabled').removeClass('hide');
    } else {
      enable = 0;
      parentZone.find('.disabled').removeClass('hide');
    }

    $.ajax({
      url: `index.php?controller=AdminCurrencies&token=${token}`,
      cache: false,
      data: `ajax=1&action=cronjobLiveExchangeRate&tab=AdminCurrencies&enable=${enable}`,
    });
  });

  // Order details: show modal to update shipping details
  $(document).on('click', '.edit_shipping_link', function (e) {
    e.preventDefault();

    $('#id_order_carrier').val($(this).data('id-order-carrier'));
    $('#shipping_tracking_number').val($(this).data('tracking-number'));
    $(`#shipping_carrier option[value=${$(this).data('id-carrier')}]`).prop('selected', true);

    $('#modal-shipping').modal();
  });
});
