/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Router from '@components/router';
import OrderViewPageMap from '@pages/order/OrderViewPageMap';

/* eslint-disable */
interface SearchParams extends Record<string, any> {
  search_phrase: string;
  currency_id?: number;
  order_id?: number;
}
/* eslint-enable */

const {$} = window;

export default class OrderProductAutocomplete {
  activeSearchRequest: null | JQuery.jqXHR;

  router: Router;

  input: JQuery;

  results: Array<any>;

  dropdownMenu: JQuery;

  selectShipment: HTMLSelectElement;

  searchTimeoutId: undefined | number | ReturnType<typeof setTimeout>;

  onItemClickedCallback: (product?: Record<string, any> | undefined) => void;

  isMultishipmentIsEnabled: boolean;

  constructor(input: JQuery) {
    this.activeSearchRequest = null;
    this.router = new Router();
    this.input = input;
    this.results = [];
    this.searchTimeoutId = undefined;
    this.selectShipment = document.querySelector<HTMLSelectElement>(OrderViewPageMap.selectAddShipment)!;
    // eslint-disable-next-line max-len
    this.isMultishipmentIsEnabled = document.querySelector<HTMLElement>(OrderViewPageMap.productsTable)?.dataset.multishipmentEnabled === '1';

    if (this.isMultishipmentIsEnabled) {
      this.dropdownMenu = $(OrderViewPageMap.productSearchInputAutocompleteMenuOnModale);
    } else {
      this.dropdownMenu = $(OrderViewPageMap.productSearchInputAutocompleteMenu);
    }
    /**
     * Permit to link to each value of dropdown a callback after item is clicked
     */
    // eslint-disable-next-line
    this.onItemClickedCallback = () => {};
  }

  listenForSearch(): void {
    this.input.on('click', (event) => {
      event.stopImmediatePropagation();
      this.updateResults(this.results);
    });

    this.input.on('keyup', (event: JQueryEventObject) => this.delaySearch(<HTMLInputElement>event.currentTarget));
    $(document).on('click', () => this.dropdownMenu.hide());
  }

  delaySearch(input: HTMLInputElement): void {
    clearTimeout(<number> this.searchTimeoutId);
    // Search only if the search phrase length is greater than 2 characters
    if (input.value.length < 2) {
      return;
    }

    this.searchTimeoutId = setTimeout(() => {
      this.search(input.value, $(input).data('currency'), $(input).data('order'));
    }, 300);
  }

  search(search: string, currency: number, orderId: number): void {
    const params: SearchParams = {search_phrase: search};

    if (currency) {
      params.currency_id = currency;
    }

    if (orderId) {
      params.order_id = orderId;
    }

    if (this.activeSearchRequest !== null) {
      this.activeSearchRequest.abort();
    }

    this.activeSearchRequest = $.get(this.router.generate('admin_orders_products_search', params));
    this.activeSearchRequest
      .then((response) => this.updateResults(response))
      .always(() => {
        this.activeSearchRequest = null;
      });
  }

  updateResults(results: Record<string, any>): void {
    this.dropdownMenu.empty();

    if (!results || !results.products || Object.keys(results.products).length <= 0) {
      this.dropdownMenu.hide();
      return;
    }

    this.results = results.products;

    Object.values(this.results).forEach((val) => {
      const link = $(`<a class="dropdown-item" data-id="${val.productId}" href="#">${val.name}</a>`);

      link.on('click', (event) => {
        event.preventDefault();
        this.onItemClicked($(event.target).data('id'));
      });

      this.dropdownMenu.append(link);
    });

    this.dropdownMenu.show();
  }

  onItemClicked(id: number): void {
    const selectedProduct = this.results.filter((product) => product.productId === id);

    if (selectedProduct.length !== 0) {
      this.input.val(selectedProduct[0].name);
      if (this.selectShipment) {
        const shipmentSelectorContainer = document.querySelector<HTMLElement>(OrderViewPageMap.selectAddShipmentContainer)!;
        shipmentSelectorContainer.classList.toggle('d-none', selectedProduct[0].virtual === true);

        if (selectedProduct[0].virtual === false) {
          this.populateShipmentSelect(id);
        }
      }
      this.onItemClickedCallback(selectedProduct[0]);
    }
  }

  populateShipmentSelect(productId: number): void {
    const orderId = Number(this.input.data('order'));

    if (!orderId || !productId) {
      throw new Error('Missing orderId or productId, cant fetch shipment for product');
    }

    this.selectShipment.disabled = true;

    fetch(this.router.generate('admin_orders_get_shipments_for_product', {orderId, productId}), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('An error occured while fetching shipments');
        }
        return response.json();
      })
      .then((data) => {
        this.selectShipment.innerHTML = '';

        data.shipments.forEach(
          ({id, name}: { id: string; name: string }) => {
            this.selectShipment.append(
              new Option(name, id),
            );
          },
        );

        this.selectShipment.disabled = false;
      })
      .catch((error) => {
        console.error('An error occured while fetching shipments ', error);
        this.selectShipment.disabled = true;
      });
  }
}
