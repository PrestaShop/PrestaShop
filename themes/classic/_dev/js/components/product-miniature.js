/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';

export default class ProductMinitature {
  init() {
    $('.js-product-miniature').each((index, element) => {
      const FLAG_MARGIN = 10;
      //Top on sale banner
      const onSaleElems =  $(element).find('.on-sale');
      //Discount flag
      const discountElems = $(element).find('.discount-product');
      //Flags other than on-sale, discount and online-only (which have all their way to display)
      const flagElems = $(element).find('.product-flag:not(.on-sale):not(.discount):not(.online-only)');

      let flagsTop = FLAG_MARGIN;
      let discountTop = FLAG_MARGIN;
      if (onSaleElems.length) {
        discountTop = onSaleElems.outerHeight() + FLAG_MARGIN;
      }

      if (discountElems.length) {
        flagsTop = discountTop + discountElems.outerHeight() + FLAG_MARGIN;
        //Discount flag is actually in product-description div so it needs a negative top value
        discountElems.css('top', discountTop + -$(element).find('.thumbnail-container').outerHeight() + $(element).find('.product-description').outerHeight());
      }

      //Now display flags one above the other
      flagElems.each((index, flag) => {
        $(flag).css('top', flagsTop);
        $(flag).css('margin-top', 0);
        flagsTop += $(flag).outerHeight() + FLAG_MARGIN;
      });

      //Limit number of shown colors
      if ($(element).find('.color').length > 5) {
        let count = 0;

        $(element).find('.color').each((index, element) => {
          if (index > 4) {
            $(element).hide();
            count ++;
          }
        });

        $(element).find('.js-count').append(`+${count}`);
      }
    });
  }
}
