import $ from 'jquery';
import productHeader from './product-header';
import productSearchAutocomplete from './product-search-autocomplete';
import categoryTree from './category-tree';
import attributes from './attributes';
import bulkCombination from './product-bulk-combinations'
import nestedCategory from './nested-categories'
import combination from './combination'

$(() => {
  productHeader();
  productSearchAutocomplete();
  categoryTree();
  attributes();
  combination();
  bulkCombination().init();
  nestedCategory().init();

  // This is the only script for the module page so there is no specific file for it.
  $('.modules-list-select').on("change", (e) => {
    $('.module-render-container').hide();
    $(`.${e.target.value}`).show();
  });
});
