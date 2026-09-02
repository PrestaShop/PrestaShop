/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default function () {
  /**
   * scroll right to show radio buttons on the category tree
   */
  const scrollCategoryTree = function scrollCategoryTree() {
    const $categoryTreeOverflow = $('.category-tree-overflow');
    const leftPos = $categoryTreeOverflow.width();
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

  $('#categories-tree-expand').on('click', () => {
    treeAction('expand');
    $('#categories-tree-expand').hide();
    $('#categories-tree-reduce').show();
  });
  $('#categories-tree-reduce').on('click', () => {
    treeAction('collapse');
    $('#categories-tree-reduce').hide();
    $('#categories-tree-expand').show();
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
