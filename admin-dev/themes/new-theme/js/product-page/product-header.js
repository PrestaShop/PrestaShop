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

export default function() {
  let tabWidth = 0;
  let navWidth = 50;

  $(window).on('resize', () => {
    init();
  });

  $('.js-nav-tabs li').each((index, item) => {
    navWidth += $(item).width();
    $('.js-nav-tabs').width(navWidth);
  });

  $('.js-arrow').on('click', (e) => {
    tabWidth = navWidth - $('.js-tabs').width();

    if ($('.js-arrow').is(':visible')) {
      $('.js-nav-tabs').animate({
        left: $(e.currentTarget).hasClass('right-arrow') ? `-=${tabWidth}` : 0
      }, 400, 'easeOutQuad', () => {
        if ($(e.currentTarget).hasClass('right-arrow')) {
          $('.left-arrow').addClass('visible');
          $('.right-arrow').removeClass('visible');
        } else {
          $('.right-arrow').addClass('visible');
          $('.left-arrow').removeClass('visible');
        }
      });
    }
  });

  var init = () => {
    if($('.js-nav-tabs').width() < $('.js-tabs').width()) {
      $('.js-nav-tabs').width($('.js-tabs').width());
      return $('.js-arrow').hide();
    }
    else {
      $('.js-arrow').show();
    }
  };

  init();
}
