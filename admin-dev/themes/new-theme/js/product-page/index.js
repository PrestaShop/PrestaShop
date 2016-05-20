import $ from 'jquery';
import productHeader from './product-header';
import productSearchAutocomplete from './product-search-autocomplete';
import categoryTree from './category-tree';
import attributes from './attributes';
import bulkCombination from './product-bulk-combinations'

$(() => {
  productHeader();
  productSearchAutocomplete();
  categoryTree();
  attributes();
  var bulkCombination_ = bulkCombination()
  bulkCombination_.init();
});
