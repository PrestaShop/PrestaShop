import EntitySearchInput from '@components/entity-search-input';
import ProductEventMap from '@pages/product/product-event-map';

const {$} = window;

$(() => {
  //@todo: move selectors to map
  new EntitySearchInput($('#specific_price_customer_id'), {
    responseTransformer: (response: any) => {
      if (!response) {
        return [];
      }

      return response.customers;
    },
  });
});
