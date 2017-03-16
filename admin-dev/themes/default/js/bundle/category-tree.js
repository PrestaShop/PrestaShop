/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

(function ($) {

  $.fn.categorytree = function (settings) {

    var isMethodCall = (typeof settings === 'string'), // is this a method call like $().categorytree("unselect")
      returnValue = this;
    // if a method call execute the method on all selected instances
    if (isMethodCall) {
      switch (settings) {
        case 'unselect':
          $('div.radio > label > input:radio', this).prop('checked', false);
          // TODO: add a callback method feature?
          break;
        case 'unfold':
          $('ul', this).show();
          $('li', this).has('ul').addClass('less');
          break;
        case 'fold':
          $('ul ul', this).hide();
          $('li', this).has('ul').addClass('more');
          break;
        default:
          throw 'Unknown method';
      }
    }
    // initialize tree
    else {
      $('li > ul', this).each(function (i, item) {
        var clickHandler = function (event) {

          var $ui = $(event.target);
          if ($ui.attr('type') === 'radio' || $ui.attr('type') === 'checkbox') {
            return;
          } else {
            event.stopPropagation();
          }

          if ($ui.next('ul').length === 0) {
            $ui = $ui.parent();
          }

          $ui.next('ul').toggle();
          if ($ui.next('ul').is(':visible')) {
            $ui.parent('li').removeClass().addClass('less');
          } else {
            $ui.parent('li').removeClass().addClass('more');
          }

          return false;
        };

        var $inputWrapper = $(item).prev('div');
        $inputWrapper.on('click', clickHandler);
        $inputWrapper.find('label').on('click', clickHandler);

        if ($(item).is(':visible')) {
          $(item).parent('li').removeClass().addClass('less');
        } else {
          $(item).parent('li').removeClass().addClass('more');
        }
      });
    }
    // return the jquery selection (or if it was a method call that returned a value - the returned value)
    return returnValue;
  };
})(jQuery);
