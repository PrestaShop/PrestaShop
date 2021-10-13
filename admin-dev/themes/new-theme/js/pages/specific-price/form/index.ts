import EntitySearchInput from '@components/entity-search-input';
import ProductEventMap from '@pages/product/product-event-map';

const {$} = window;

$(() => {
  console.log('init');
  new EntitySearchInput($('#specific_price_customer_id'), {
    onRemovedContent: () => {
      console.log('test');
    },
    onSelectedContent: () => {
      console.log('test');
    },
  });
});
