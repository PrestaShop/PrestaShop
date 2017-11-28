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

  /**
   * scroll right to show radio buttons on the category tree
   */
  const scrollCategoryTree = function scrollCategoryTree() {
    let $categoryTreeOverflow = $('.category-tree-overflow');
    let leftPos = $categoryTreeOverflow.width();
    $categoryTreeOverflow.animate({scrollLeft: leftPos}, 200);
  };

  const treeAction = (treeState) => {
    if (treeState === 'expand') {
      $('.js-categories-tree ul').show();
      $('.more').toggleClass('less');
      // scroll right to see the radio buttons
      scrollCategoryTree();
    } else {
      $('.js-categories-tree ul:not(.category-tree)').hide();
      $('.less').toggleClass('more');
    }
  };

  $('.js-categories-tree-actions').on('click', (e) => {
    if ($(e.target).data('action') === 'expand' || $(e.target).parent().data('action') === 'expand') {
      treeAction('expand');
    } else {
      treeAction('reduce');
    }
  });

  // scroll right to see the radio buttons
  $('.category-tree-overflow .checkbox').on('click', (e) => {
    if (!$(e.target).is('input')) {
        // do not scroll if (un)checking some inputs
        scrollCategoryTree();
    }
  });

  $('.category-tree-overflow .checkbox label').on('click', (e) => {
    if (!$(e.target).is('input')) {
        // do not scroll if (un)checking some inputs
        scrollCategoryTree();
    }
  });
}
