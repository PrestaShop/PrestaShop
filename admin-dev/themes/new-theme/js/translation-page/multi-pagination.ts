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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

export default function (paginationContainer: JQuery): void {
  const lng = paginationContainer.find('.js-page-link').length;
  const multi = '<li class="page-item js-multi"><span class="page-link">...</span></li>';
  const displayNumber = paginationContainer.data('display-number'); // Number of pages to display after the first
  let current = paginationContainer
    .find('.page-item.active')
    .data('page-index');

  checkCurrentPage(current);

  paginationContainer
    .find('.js-page-link')
    .on('click', (event: JQueryEventObject) => {
      event.preventDefault();
      paginationContainer.find('.active').removeClass('active');
      $(event.currentTarget)
        .parent()
        .addClass('active');
      current = $(event.currentTarget)
        .parent()
        .data('page-index');
      checkCurrentPage(current);
    });

  paginationContainer
    .find('.js-arrow')
    .on('click', (event: JQueryEventObject) => {
      current = paginationContainer
        .find('.page-item.active')
        .data('page-index');
      event.preventDefault();
      const direction = $(event.currentTarget).data('direction');

      if (
        direction === 'prev'
        && !$(event.currentTarget)
          .parent()
          .next()
          .hasClass('active')
      ) {
        $(`.page[data-page-index=${current - 1}]`).removeClass('hide');
        $(`.page[data-page-index=${current}]`).addClass('hide');
        $(`.page-item[data-page-index=${current - 1}]`).addClass('active');
        $(`.page-item[data-page-index=${current}]`).removeClass('active');
        current -= 1;
      } else if (
        direction === 'next'
        && !$(event.currentTarget)
          .parent()
          .prev()
          .hasClass('active')
      ) {
        $(`.page[data-page-index=${current + 1}]`).removeClass('hide');
        $(`.page[data-page-index=${current}]`).addClass('hide');
        $(`.page-item[data-page-index=${current + 1}]`).addClass('active');
        $(`.page-item[data-page-index=${current}]`).removeClass('active');
        current += 1;
      }
      if (
        $(event.currentTarget).data('direction') === 'prev'
        && current === 1
      ) {
        return false;
      }

      checkCurrentPage(current);

      return true;
    });

  function checkCurrentPage(currentEl: number) {
    $('.pagination').each((_index, pagination) => {
      const pagContainer = $(pagination);
      const prevDots = pagContainer
        .find('[data-page-index=1]')
        .next('.js-multi');
      const nextDots = pagContainer
        .find(`[data-page-index=${lng}]`)
        .prev('.js-multi');
      const mid = Math.round(displayNumber);

      pagContainer.find('.js-page-link').each((index, item) => {
        if (
          currentEl >= displayNumber + 1
          && index === 0
          && prevDots.length === 0
        ) {
          $(item)
            .parent()
            .after(multi);
        }
        if (currentEl >= displayNumber + 1) {
          if (index >= currentEl - mid && index <= currentEl + mid) {
            $(item).show();
            if (
              lng - currentEl >= mid
              && index > currentEl
              && index !== lng - 1
            ) {
              $(item).hide();
            } else if (
              nextDots.length === 0
              && index === lng - 1
              && lng - currentEl > displayNumber
            ) {
              $(item)
                .parent()
                .before(multi);
            }
          } else if (
            index !== 0
            && index !== lng - 1
            && lng - 1 - index > displayNumber
          ) {
            $(item).hide();
            if (nextDots.length && lng - displayNumber <= currentEl) {
              nextDots.remove();
            }
          } else if (currentEl === lng) {
            nextDots.remove();
            if (index <= displayNumber && index !== 0) {
              $(item).hide();
            } else {
              $(item).show();
            }
          }
        } else if (
          currentEl
          && index > displayNumber
          && index !== lng - 1
          && currentEl < displayNumber
        ) {
          $(item).hide();
        } else if (
          index === lng - 1
          && currentEl === 1
          && nextDots.length === 0
        ) {
          $(item)
            .parent()
            .before(multi);
        } else if (index > displayNumber && index !== lng - 1) {
          $(item).hide();
        } else if (
          index === lng - 1
          && nextDots.length === 0
          && currentEl > 1
        ) {
          $(item)
            .parent()
            .before(multi);
        } else {
          $(item).show();
          if (index === 0 && prevDots.length !== 0) {
            prevDots.remove();
          }
        }
      });
    });
  }
}
