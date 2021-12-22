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

import ProductMap from '@pages/product/product-map';

const {$} = window;

export default function () {
  const $defaultArrowWidth = 35;
  const $arrow = $(ProductMap.jsArrow);
  const $tabs = $(ProductMap.jsTabs);
  const $navTabs = $(ProductMap.jsNavTabs);

  let $positions;
  let $moveTo = 0;
  let $tabWidth = 0;
  let $navWidth = $defaultArrowWidth;
  let $widthWithTabs = 0;

  $navTabs.find('li').each((index, item) => {
    $navWidth += $(item).width();
  });

  $widthWithTabs = $navWidth + $defaultArrowWidth * 2;

  $navTabs.width($widthWithTabs);

  $navTabs.find(ProductMap.toggleTab).on('click', (e) => {
    if (!$(e.target).hasClass('active')) {
      $(ProductMap.formContentTab).removeClass('active');
    }
  });

  $arrow.on('click', (e) => {
    if ($arrow.is(':visible')) {
      $tabWidth = $tabs.width();
      $positions = $navTabs.position();

      $moveTo = '-=0';
      if ($(e.currentTarget).hasClass('right-arrow')) {
        if ($tabWidth - $positions.left < $navWidth) {
          $moveTo = `-=${$tabWidth}`;
        }
      } else if ($positions.left < $defaultArrowWidth) {
        $moveTo = `+=${$tabWidth}`;
      }

      $navTabs.animate(
        {
          left: $moveTo,
        },
        400,
        'easeOutQuad',
        () => {
          $(ProductMap.leftArrow).toggleClass('visible', $(e.currentTarget).hasClass('right-arrow'));
          $(ProductMap.rightArrow).toggleClass('visible', !$(e.currentTarget).hasClass('right-arrow'));
        },
      );
    }
  });
}
