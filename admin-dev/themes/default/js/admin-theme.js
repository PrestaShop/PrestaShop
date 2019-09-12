/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

//build confirmation modal
function confirm_modal(heading, question, left_button_txt, right_button_txt, left_button_callback, right_button_callback) {
  var confirmModal =
    $('<div class="bootstrap modal hide fade">' +
      '<div class="modal-dialog">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<a class="close" data-dismiss="modal" >&times;</a>' +
      '<h3>' + heading + '</h3>' +
      '</div>' +
      '<div class="modal-body">' +
      '<p>' + question + '</p>' +
      '</div>' +
      '<div class="modal-footer">' +
      '<a href="#" id="confirm_modal_left_button" class="btn btn-primary">' +
      left_button_txt +
      '</a>' +
      '<a href="#" id="confirm_modal_right_button" class="btn btn-primary">' +
      right_button_txt +
      '</a>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '</div>');
  confirmModal.find('#confirm_modal_left_button').click(function () {
    left_button_callback();
    confirmModal.modal('hide');
  });
  confirmModal.find('#confirm_modal_right_button').click(function () {
    right_button_callback();
    confirmModal.modal('hide');
  });
  confirmModal.modal('show');
}

//build error modal
/* global error_continue_msg */
function error_modal(heading, msg) {
  var errorModal =
    $('<div class="bootstrap modal hide fade">' +
      '<div class="modal-dialog">' +
      '<div class="modal-content">' +
      '<div class="modal-header">' +
      '<a class="close" data-dismiss="modal" >&times;</a>' +
      '<h4>' + heading + '</h4>' +
      '</div>' +
      '<div class="modal-body">' +
      '<p>' + msg + '</p>' +
      '</div>' +
      '<div class="modal-footer">' +
      '<a href="#" id="error_modal_right_button" class="btn btn-default">' +
      error_continue_msg +
      '</a>' +
      '</div>' +
      '</div>' +
      '</div>' +
      '</div>');
  errorModal.find('#error_modal_right_button').click(function () {
    errorModal.modal('hide');
  });
  errorModal.modal('show');
}

//move to hash after clicking on anchored links
function scroll_if_anchor(href) {
  href = typeof(href) === "string" ? href : $(this).attr("href");
  var fromTop = 120;
  if(href.indexOf("#") === 0) {
    var $target = $(href);
    if($target.length) {
      $('html, body').animate({ scrollTop: $target.offset().top - fromTop });
      if(history && "pushState" in history) {
        history.pushState({}, document.title, window.location.href + href);
        return false;
      }
    }
  }
}

$(document).ready(function() {
    $(".nav-bar").find(".link-levelone").hover(function() {
        $(this).addClass("-hover");
    }, function() {
        $(this).removeClass("-hover");
    });

    $('.nav-bar li.link-levelone.has_submenu > a').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let $submenu = $(this).parent();
        $('.nav-bar li.link-levelone.has_submenu a > i.material-icons.sub-tabs-arrow')
            .text('keyboard_arrow_down');
        let onlyClose = $(e.currentTarget).parent().hasClass('ul-open');

        if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
            $('.nav-bar li.link-levelone.has_submenu.ul-open').removeClass('ul-open open -hover');
            $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').removeAttr('style');
        } else {
            $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').slideUp({
                complete: function() {
                    $(this).parent().removeClass('ul-open open');
                    $(this).removeAttr('style');
                }
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
                complete: function() {
                    $submenu.addClass('open');
                    $(this).removeAttr('style');
                }
            });
        }
        $submenu.find('i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
    });

    $('.nav-bar').on('click', '.menu-collapse', function() {
        $('body').toggleClass('page-sidebar-closed');

        if ($('body').hasClass('page-sidebar-closed')) {
            $('nav.nav-bar ul.main-menu > li')
                .removeClass('ul-open open')
                .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
            addMobileBodyClickListener();
        } else {
            $('nav.nav-bar ul.main-menu > li.-active')
                .addClass('ul-open open')
                .find('a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_up');
            $('body').off('click.mobile');
        }

        $.ajax({
            url: $(this).data('toggle-url'),
            type: 'post',
            cache: false,
            data: {
                shouldCollapse: Number($('body').hasClass('page-sidebar-closed'))
            },
        });
    });
    addMobileBodyClickListener();
    const MAX_MOBILE_WIDTH = 1023;

    if ($(window).width() <= MAX_MOBILE_WIDTH) {
        mobileNav(MAX_MOBILE_WIDTH);
    }

    $(window).on('resize', () => {
        if ($('body').hasClass('mobile') && $(window).width() > MAX_MOBILE_WIDTH) {
            unbuildMobileMenu();
        } else if (!$('body').hasClass('mobile') && $(window).width() <= MAX_MOBILE_WIDTH) {
            mobileNav(MAX_MOBILE_WIDTH);
        }
    });

    function addMobileBodyClickListener() {
        if (!$('body').is('.page-sidebar-closed:not(.mobile)')) {
            return;
        }
        // To close submenu on mobile devices
        $('body').on('click.mobile', function() {
            if ($('ul.main-menu li.ul-open').length > 0) {
                $('.nav-bar li.link-levelone.has_submenu.ul-open').removeClass('ul-open open -hover');
                $('.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu').removeAttr('style');
            }
        });
    }

    function mobileNav() {
        let $logout = $('#header_logout').addClass('link').removeClass('m-t-1').prop('outerHTML');
        var $employee = $('.employee_avatar');

        // Legacy
        if ($('#employee_links').length > 0) {
            $employee = $('<a class="employee_avatar">').attr('href', $('#employee_infos > a.employee_name').attr('href'));
            $employee.append($('#employee_links .employee_avatar').clone());
            $employee.append($('<span></span>').append($('#employee_links > .username').text()));
        }
        let profileLink = $('.profile-link').attr('href');

        $('.nav-bar li.link-levelone.has_submenu:not(.open) a > i.material-icons.sub-tabs-arrow').text('keyboard_arrow_down');
        $('body').addClass('mobile');
        $('.nav-bar').addClass('mobile-nav').attr('style', 'margin-left: -100%;');
        $('.panel-collapse').addClass('collapse');
        $('.link-levelone a').each((index, el)=> {
            let id = $(el).parent().find('.collapse').attr('id');
            if(id) {
                $(el).attr('href', `#${id}`).attr('data-toggle','collapse');
            }
        });
        $('.main-menu').append(`<li class="link-levelone" data-submenu="">${$logout}</li>`);
        $('.main-menu').prepend(`<li class="link-levelone">${$employee.prop('outerHTML')}</li>`);
        $('.collapse').collapse({
            toggle: false
        });
        if ($('#employee_links').length === 0) {
            $('.employee_avatar .material-icons, .employee_avatar span').wrap(`<a href="${profileLink}"></a>`);
        }
        $('.js-mobile-menu').on('click', expand);
        $('.js-notifs_dropdown').css({
            'height' : window.innerHeight
        });

        function expand(e) {
            if ($('div.notification-center.dropdown').hasClass('open')) {
                return;
            }

            if ($('.mobile-nav').hasClass('expanded')) {
                $('.mobile-nav').animate({'margin-left': '-100%'}, {
                    complete: function() {
                        $('.nav-bar, .mobile-layer').removeClass('expanded');
                        $('.nav-bar, .mobile-layer').addClass('d-none');
                    }
                });
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
        $('.nav-bar').removeClass('mobile-nav expanded').addClass('d-none').css('margin-left', 0);
        $('.js-mobile-menu').off();
        $('.panel-collapse').removeClass('collapse').addClass('submenu');
        $('.shop-list-title').remove();
        $('.js-non-responsive').hide();
        $('.mobile-layer').addClass('d-none').removeClass('expanded');
    }

  //scroll top
  function animateGoTop() {
    if ($(window).scrollTop()) {
      $('#go-top:hidden').stop(true, true).fadeIn();
      $('#go-top:hidden').removeClass('hide');
    } else {
      $('#go-top').stop(true, true).fadeOut();
    }
  }

  //media queries - depends of enquire.js
  /*global enquire*/
  enquire.register('screen and (max-width: 1200px)', {
    match : function() {
      if( $('#main').hasClass('helpOpen')) {
        $('.toolbarBox a.btn-help').trigger('click');
      }
    },
    unmatch : function() {

    }
  });

  //bootstrap components init
  $('.dropdown-toggle').dropdown();
  $('.label-tooltip, .help-tooltip').tooltip();
  $('#error-modal').modal('show');

  // go on top of the page
  $('#go-top').on('click',function() {
    $('html, body').animate({ scrollTop: 0 }, 'slow');
    return false;
  });

  var timer;
  $(window).scroll(function() {
    if(timer) {
      window.clearTimeout(timer);
    }
    timer = window.setTimeout(function() {
      animateGoTop();
    }, 100);
  });

  // search with nav sidebar closed
  $(document).on('click', '.page-sidebar-closed .searchtab' ,function() {
    $(this).addClass('search-expanded');
    $(this).find('#bo_query').focus();
  });

  $('.page-sidebar-closed').click(function() {
    $('.searchtab').removeClass('search-expanded');
  });

  $('#header_search button').on('click', function(e){
    e.stopPropagation();
  });

  //erase button search input
  if ($('#bo_query').val() !== '') {
    $('.clear_search').removeClass('hide');
  }

  $('.clear_search').on('click', function(e){
    e.stopPropagation();
    e.preventDefault();
    var id = $(this).closest('form').attr('id');
    $('#'+id+' #bo_query').val('').focus();
    $('#'+id+' .clear_search').addClass('hide');
  });
  $('#bo_query').on('keydown', function(){
    if ($('#bo_query').val() !== ''){
      $('.clear_search').removeClass('hide');
    }
  });

  //search with nav sidebar opened
  $('.page-sidebar').click(function() {
    $('#header_search .form-group').removeClass('focus-search');
  });

  $('#header_search #bo_query').on('click', function(e){
    e.stopPropagation();
    e.preventDefault();
    if($('body').hasClass('mobile-nav')){
      return false;
    }
    $('#header_search .form-group').addClass('focus-search');
  });

  //select list for search type
  $('#header_search_options').on('click','li a', function(e){
    e.preventDefault();
    $('#header_search_options .search-option').removeClass('active');
    $(this).closest('li').addClass('active');
    $('#bo_search_type').val($(this).data('value'));
    $('#search_type_icon').removeAttr("class").addClass($(this).data('icon'));
    $('#bo_query').attr("placeholder",$(this).data('placeholder'));
    $('#bo_query').focus();
  });

  // reset form
  /* global header_confirm_reset, body_confirm_reset, left_button_confirm_reset, right_button_confirm_reset */
  $(".reset_ready").click(function () {
    var href = $(this).attr('href');
    confirm_modal( header_confirm_reset, body_confirm_reset, left_button_confirm_reset, right_button_confirm_reset,
      function () {
        window.location.href = href + '&keep_data=1';
      },
      function () {
        window.location.href = href + '&keep_data=0';
    });
    return false;
  });

  //scroll_if_anchor(window.location.hash);
  $("body").on("click", "a.anchor", scroll_if_anchor);

  //manage curency status switcher
  $('#currencyStatus input').change(function(){
    var parentZone = $(this).parent().parent().parent().parent();
    parentZone.find('.status').addClass('hide');

    if($(this).attr('checked') == 'checked'){
      parentZone.find('.enabled').removeClass('hide');
      $('#currency_form #active').val(1);
    }else{
      parentZone.find('.disabled').removeClass('hide');
      $('#currency_form #active').val(0);
    }
  });


  $('#currencyCronjobLiveExchangeRate input').change(function(){
    var enable = 0;
    var parentZone = $(this).parent().parent().parent().parent();
    parentZone.find('.status').addClass('hide');

    if($(this).attr('checked') == 'checked'){
      enable = 1;
      parentZone.find('.enabled').removeClass('hide');
    }else{
      enable = 0;
      parentZone.find('.disabled').removeClass('hide');
    }

    $.ajax({
      url: "index.php?controller=AdminCurrencies&token="+token,
      cache: false,
      data: "ajax=1&action=cronjobLiveExchangeRate&tab=AdminCurrencies&enable="+enable
    });
  });

  // Order details: show modal to update shipping details
  $(document).on('click', '.edit_shipping_link', function(e) {
    e.preventDefault();

    $('#id_order_carrier').val($(this).data('id-order-carrier'));
    $('#shipping_tracking_number').val($(this).data('tracking-number'));
    $('#shipping_carrier option[value='+$(this).data('id-carrier')+']').prop('selected', true);

    $('#modal-shipping').modal();
  });
});
