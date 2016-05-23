import $ from 'jquery';
import productHeader from './product-header';
import productSearchAutocomplete from './product-search-autocomplete';
import categoryTree from './category-tree';
import attributes from './attributes';

$(() => {
  productHeader();
  productSearchAutocomplete();
  categoryTree();
  attributes();
});
