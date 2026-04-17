/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
