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

export default function(paginationContainer) {

  const lng = paginationContainer.find('.js-page-link').length;
  const multi = '<li class="page-item js-multi"><span class="page-link">...</span></li>';
  const displayNumber = paginationContainer.data('display-number'); // Number of pages to display after the first
  let current = paginationContainer.find('.page-item.active').data('page-index');

  checkCurrentPage(current);

  paginationContainer.find('.js-page-link').on('click', function(event) {
      event.preventDefault();
      paginationContainer.find('.active').removeClass('active');
      $(event.currentTarget).parent().addClass('active');
      current = $(event.currentTarget).parent().data('page-index');
      checkCurrentPage(current);
  });

  paginationContainer.find('.js-arrow').on('click', function(event) {
      current = paginationContainer.find('.page-item.active').data('page-index');
      event.preventDefault();
      if($(event.currentTarget).data('direction') === 'prev' && !$(event.currentTarget).parent().next().hasClass('active')) {
          $(`.page[data-page-index=${current - 1}]`).removeClass('hide');
          $(`.page[data-page-index=${current}]`).addClass('hide');
          $(`.page-item[data-page-index=${current - 1}]`).addClass('active');
          $(`.page-item[data-page-index=${current}]`).removeClass('active');
          current --;
      }
      else if($(event.currentTarget).data('direction') === 'next' && !$(event.currentTarget).parent().prev().hasClass('active')) {
        $(`.page[data-page-index=${current + 1}]`).removeClass('hide');
        $(`.page[data-page-index=${current}]`).addClass('hide');
        $(`.page-item[data-page-index=${current + 1}]`).addClass('active');
        $(`.page-item[data-page-index=${current}]`).removeClass('active');
        current ++;
      }
      if($(event.currentTarget).data('direction') === 'prev' && current === 1) {
        return false;
      }
      checkCurrentPage(current);
  });

function checkCurrentPage(current) {
  $('.pagination').each((index, pagination) => {

    var paginationContainer = $(pagination);
    var prevDots = paginationContainer.find('[data-page-index=1]').next('.js-multi');
    var nextDots = paginationContainer.find('[data-page-index='+lng+']').prev('.js-multi');
    var mid = Math.round(displayNumber);

    paginationContainer.find('.js-page-link').each(function(index, item) {
        if(current >= displayNumber + 1 && index === 0 && prevDots.length === 0) {
          $(item).parent().after(multi);
        }
        if(current >= displayNumber + 1 ) {
          if(index >= current - mid && index <= current + mid) {
            $(item).show();
            if(lng - current >= mid && index > current && index !== lng - 1) {
              $(item).hide();
            }
            else if(nextDots.length === 0 && index === lng -1 && lng - current > displayNumber) {
              $(item).parent().before(multi);
            }
          }
          else if(index !== 0 && index !== lng-1 && (lng-1 - index) > displayNumber) {
            $(item).hide();
            if(nextDots.length && lng - displayNumber <= current) {
              nextDots.remove();
            }
          }
          else if(current === lng){
            nextDots.remove();
            if(index <= displayNumber && index !==0) {
              $(item).hide();
            }
            else {
              $(item).show();
            }
          }
        }
        else if(current && index > displayNumber && index !== lng-1 && current < displayNumber) {
          $(item).hide();
        }
        else if(index === lng-1 && current === 1 && nextDots.length === 0) {
          $(item).parent().before(multi);
        }
        else {
          if(index > displayNumber && index !== lng-1) {
            $(item).hide();
          }
          else if(index === lng-1 && nextDots.length === 0 && current > 1) {
            $(item).parent().before(multi);
          }
          else {
            $(item).show();
            if(index === 0 && prevDots.length !== 0) {
              prevDots.remove();
            }
          }
        }
      });
    });
  }
}
