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
  var bulkCombination_ = bulkCombination()
  bulkCombination_.init();
  var nestedCategory_ = nestedCategory()
  nestedCategory_.init();
});
