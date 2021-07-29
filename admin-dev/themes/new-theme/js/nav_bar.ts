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

import PerfectScrollbar from 'perfect-scrollbar';
import 'perfect-scrollbar/css/perfect-scrollbar.css';
import getAnimationEvent from './app/utils/animations';
import NavbarTransitionHandler from './components/navbar-transition-handler';
import GlobalMap from './global-map';

const {$} = window;

export default class NavBar {
  constructor() {
    $(() => {
      const $mainMenu = $('.main-menu');
      const $navBar = $('.nav-bar');
      const $body = $('body');

      if ($navBar.length > 0) {
        const $navBarOverflow = $('.nav-bar-overflow');
        const NavBarTransitions = new (NavbarTransitionHandler as any)(
          $navBar,
          $mainMenu,
          getAnimationEvent('transition', 'end'),
          $body,
        );

        if ($navBarOverflow.length > 0) {
          new PerfectScrollbar('.nav-bar-overflow');
          $navBarOverflow.on('scroll', () => {
            const $menuItems = $(GlobalMap.navBar.menuItems);

            $($menuItems).each((i, e) => {
              const itemOffsetTop = $(e).position().top;
              $(e)
                .find('ul.submenu')
                .css('top', itemOffsetTop);
            });
          });
        }

        $navBar.find('.link-levelone').hover(
          function onMouseEnter() {
            const itemOffsetTop = $(this).position().top;
            $(this).addClass('link-hover');
            $(this)
              .find('ul.submenu')
              .css('top', itemOffsetTop);
          },
          function onMouseLeave() {
            $(this).removeClass('link-hover');
          },
        );

        $(GlobalMap.navBar.menuItemLink).on('click', function onNavBarClick(e) {
          e.preventDefault();
          e.stopPropagation();

          NavBarTransitions.toggle();

          const $submenu = $(this).parent();
          $(GlobalMap.navBar.menuArrow).text('keyboard_arrow_down');
          const onlyClose = $(e.currentTarget)
            .parent()
            .hasClass('ul-open');

          if ($('body').is('.page-sidebar-closed:not(.mobile)')) {
            $(GlobalMap.navBar.levelOneOpenedList).removeClass(
              'ul-open open submenu-hover',
            );
            $(GlobalMap.navBar.levelOneOpenedSubmenu).removeAttr('style');
          } else {
            $(GlobalMap.navBar.levelOneOpenedSubmenu).slideUp({
              complete: function slideUpIsComplete() {
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
            $submenu.addClass('submenu-hover');
            $submenu.find('ul.submenu').removeAttr('style');
          } else {
            $submenu.find('ul.submenu').slideDown({
              complete: function slideDownIsComplete() {
                $submenu.addClass('open');
                $(this).removeAttr('style');
              },
            });
          }
          $submenu
            .find('i.material-icons.sub-tabs-arrow')
            .text('keyboard_arrow_up');

          const itemOffsetTop = $submenu.position().top;
          $submenu.find('ul.submenu').css('top', itemOffsetTop);
        });

        $navBar.on('click', '.menu-collapse', function onNavBarClick() {
          $('body').toggleClass('page-sidebar-closed');

          NavBarTransitions.toggle();

          $('.popover.show').remove();
          $('.help-box[aria-describedby]').removeAttr('aria-describedby');

          if ($('body').hasClass('page-sidebar-closed')) {
            $('nav.nav-bar ul.main-menu > li')
              .removeClass('ul-open open')
              .find('a > i.material-icons.sub-tabs-arrow')
              .text('keyboard_arrow_down');
            addMobileBodyClickListener();
          } else {
            $('nav.nav-bar ul.main-menu > li.link-active')
              .addClass('ul-open open')
              .find('a > i.material-icons.sub-tabs-arrow')
              .text('keyboard_arrow_up');
            $('body').off('click.mobile');
          }

          $.post({
            url: $(this).data('toggle-url'),
            cache: false,
            data: {
              shouldCollapse: Number($('body').hasClass('page-sidebar-closed')),
            },
          });
        });

        addMobileBodyClickListener();
        const MAX_MOBILE_WIDTH = 1023;
        const windowWidth = <number>$(window).width();

        if (windowWidth <= MAX_MOBILE_WIDTH) {
          this.mobileNav();
        }

        $(window).on('resize', () => {
          const currentWindowWidth = <number>$(window).width();

          if (
            $('body').hasClass('mobile')
            && currentWindowWidth > MAX_MOBILE_WIDTH
          ) {
            this.unbuildMobileMenu();
          } else if (
            !$('body').hasClass('mobile')
            && currentWindowWidth <= MAX_MOBILE_WIDTH
          ) {
            this.mobileNav();
          }
        });
      }

      function addMobileBodyClickListener() {
        if (!$('body').is('.page-sidebar-closed:not(.mobile)')) {
          return;
        }
        // To close submenu on mobile devices
        $('body').on('click.mobile', () => {
          if ($('ul.main-menu li.ul-open').length > 0) {
            $('.nav-bar li.link-levelone.has_submenu.ul-open').removeClass(
              'ul-open open submenu-hover',
            );
            $(
              '.nav-bar li.link-levelone.has_submenu.ul-open ul.submenu',
            ).removeAttr('style');
          }
        });
      }
    });
  }

  mobileNav(): void {
    const $logout = $('#header_logout')
      .addClass('link')
      .removeClass('m-t-1')
      .prop('outerHTML');
    const $employee = $('.employee_avatar').prop('outerHTML');
    const profileLink = $('.profile-link').attr('href');
    const $mainMenu = $('.main-menu');

    $(
      '.nav-bar li.link-levelone.has_submenu:not(.open) a > i.material-icons.sub-tabs-arrow',
    ).text('keyboard_arrow_down');
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

    $mainMenu.append(
      `<li class='link-levelone' data-submenu=''>${$logout}</li>`,
    );
    $mainMenu.prepend(`<li class='link-levelone'>${$employee}</li>`);

    $('.collapse').collapse({
      toggle: false,
    });

    $mainMenu
      .find('.employee_avatar .material-icons, .employee_avatar span')
      .wrap(`<a href='${profileLink}'></a>`);
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
            complete: () => {
              $('.nav-bar, .mobile-layer').removeClass('expanded');
              $('.nav-bar, .mobile-layer').addClass('d-none');
            },
          },
        );
        $('.mobile-layer').off();
        return;
      }

      $('.nav-bar, .mobile-layer').addClass('expanded');
      $('.nav-bar, .mobile-layer').removeClass('d-none');
      $('.mobile-layer').on('click', expand);
      $('.mobile-nav').animate({'margin-left': 0});
    }
  }

  unbuildMobileMenu(): void {
    $('body').removeClass('mobile');
    $('body.page-sidebar-closed .nav-bar .link-levelone.open').removeClass(
      'ul-open open',
    );
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
    $('.js-non-responsive').hide();
    $('.mobile-layer')
      .addClass('d-none')
      .removeClass('expanded');
  }
}
