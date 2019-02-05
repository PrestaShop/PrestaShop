/**
 * 2007-2019 PrestaShop and Contributors
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

const $ = window.$;

export default function() {
  const $defaultArrowWidth = 35;
  const $arrow = $('.js-arrow');
  const $tabs = $('.js-tabs');
  const $navTabs = $('.js-nav-tabs');

  let $positions;
  let $moveTo = 0;
  let $tabWidth = 0;
  let $navWidth = $defaultArrowWidth;
  let $widthWithTabs = 0;

  $navTabs.find('li').each((index, item) => {
    $navWidth += $(item).width();
  });

  $navTabs.width($navWidth);

  $widthWithTabs = $navWidth + ($defaultArrowWidth * 2);

  $navTabs.find('[data-toggle="tab"]').on('click', (e) => {
    if (!$(e.target).hasClass('active')) {
      $('#form_content > .form-contenttab').removeClass('active');
    }
    if ($(e.target).attr('href') === '#step1') {
      setTimeout(() => {
        $('#description_short, #tab_description_short .description-tab').addClass('active');
      }, 100);
    }
  });

  $arrow.on('click', (e) => {
    if ($arrow.is(':visible')) {
      $tabWidth = $navWidth > $navWidth ? $navWidth - $tabs.width() : $tabs.width();
      $positions = $navTabs.position();

      $moveTo = '-=0';
      if ($(e.currentTarget).hasClass('right-arrow')) {
        if (($tabWidth - $positions.left) < $navWidth) {
          $moveTo = `-=${$tabWidth}`;
        }
      } else {
        if ($positions.left < $defaultArrowWidth) {
          $moveTo = `+=${$tabWidth}`;
        }
      }

      $navTabs.animate(
        {
          left: $moveTo
        },
        400,
        'easeOutQuad',
        () => {
          if ($(e.currentTarget).hasClass('right-arrow')) {
            $('.left-arrow').addClass('visible');
            $('.right-arrow').removeClass('visible');
          } else {
            $('.right-arrow').addClass('visible');
            $('.left-arrow').removeClass('visible');
          }
        }
      );
    }
  });
}
