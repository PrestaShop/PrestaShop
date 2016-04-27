import $ from 'jquery';

export default function() {
  const treeAction = (treeState) => {
    if (treeState === 'expand') {
      $('.js-categories-tree ul').show();
      $('.more').toggleClass('less');
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
}
