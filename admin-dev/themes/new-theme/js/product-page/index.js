/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import productHeader from './product-header';
import productSearchAutocomplete from './product-search-autocomplete';
import categoryTree from './category-tree';
import attributes from './attributes';
import bulkCombination from './product-bulk-combinations';
import nestedCategory from './nested-categories';
import combination from './combination';
import Serp from '../app/utils/serp/index';

const {$} = window;

$(() => {
  productHeader();
  productSearchAutocomplete();
  categoryTree();
  attributes();
  combination();
  bulkCombination().init();
  nestedCategory().init();

  new Serp(
    {
      container: '#serp-app',
      defaultTitle: '.serp-default-title:input',
      watchedTitle: '.serp-watched-title:input',
      defaultDescription: '.serp-default-description',
      watchedDescription: '.serp-watched-description',
      watchedMetaUrl: '.serp-watched-url:input',
    },
    $('#product_form_preview_btn').data('seo-url'),
  );

  // This is the only script for the module page so there is no specific file for it.
  $('.modules-list-select').on('change', (e) => {
    $('.module-render-container').hide();
    $(`.${e.target.value}`).show();
  });

  $('.modules-list-button').on('click', (e) => {
    const target = $(e.target).data('target');
    $('.module-selection').show();
    $('.modules-list-select').val(target).trigger('change');
    return false;
  });
});
