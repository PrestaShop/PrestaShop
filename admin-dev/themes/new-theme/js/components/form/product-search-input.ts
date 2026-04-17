/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import EntitySearchInput, {EntitySearchInputOptions} from '@components/entity-search-input';

type Product = {
  name: string,
  reference: string,
  image: string,
}

export default class ProductSearchInput extends EntitySearchInput {
  constructor(
    searchInputContainer: string,
    options: OptionsObject = <EntitySearchInputOptions> {},
  ) {
    const searchInputContainerEl = <HTMLElement> document.querySelector(searchInputContainer);
    const referenceLabel = searchInputContainerEl.dataset.referenceLabel ?? '(Ref: %s)';

    // eslint-disable-next-line no-param-reassign
    options.suggestionTemplate = (product: Product) => {
      let reference = '';

      if (product.reference) {
        reference = `<span class="product-reference">(${product.reference})</span>`;
      }

      return `<div class="search-suggestion"><img src="${product.image}" /> ${product.name}${reference}</div>`;
    };
    // eslint-disable-next-line no-param-reassign
    options.responseTransformer = (response: any) => {
      Object.keys(response).forEach((key) => {
        if (Object.prototype.hasOwnProperty.call(response, key)) {
          const combination = response[key];

          if (combination.reference) {
            // eslint-disable-next-line no-param-reassign
            response[key].reference = referenceLabel.replace('%s', combination.reference);
          }
        }
      });

      return response;
    };

    super($(searchInputContainer), options);
  }
}
