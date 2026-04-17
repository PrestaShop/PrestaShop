/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import EntitySearchInput from '@components/entity-search-input';
import DiscountMap from '@pages/discount/discount-map';
import Router from '@components/router';

interface CombinationChoice {
  combinationId: number,
  combinationName: string,
}

interface ProductCombinationsResult {
  combinations: CombinationChoice[]
}

interface jQuerySelect2Choice {
  id: number,
  text: string,
}
interface jQuerySelect2Results {
  results: jQuerySelect2Choice[]
}

const {$} = window;
export default class SpecificProducts {
  private $specificProductsSearchInput: JQuery;

  private entitySearchInput!: EntitySearchInput;

  private router: Router;

  constructor() {
    this.router = new Router();
    this.$specificProductsSearchInput = $(DiscountMap.specificProductsSearchContainer);

    this.init();
  }

  init(): void {
    if (this.$specificProductsSearchInput.length) {
      const autocompleteUrl = (document.querySelector(DiscountMap.specificProductsSearchContainer) as HTMLElement)
        ?.dataset.remoteUrl;

      this.entitySearchInput = new EntitySearchInput(
        this.$specificProductsSearchInput,
        {
          remoteUrl: autocompleteUrl,
          onSelectedContent: ($node: JQuery, item: any) => {
            const $combinationIdSelect = $(DiscountMap.specificCombinationId, $node);

            if (item.product_type === 'combinations') {
              this.initCombinationSelector($combinationIdSelect, item.id);
            }
          },
          filterSelected: false,
        },
      );
    }

    // Init specific products already present
    $(DiscountMap.specificProductItem).each((index: number, element: Element) => {
      const productId = Number(element.querySelector<HTMLInputElement>(DiscountMap.specificProductId)?.value);
      const productType = String(element.querySelector<HTMLInputElement>(DiscountMap.specificProductType)?.value);
      const $combinationIdSelect = $(DiscountMap.specificCombinationId, element);

      if (productType === 'combinations') {
        this.initCombinationSelector($combinationIdSelect, productId);
      }
    });
  }

  initCombinationSelector($combinationIdSelect: JQuery, productId: number): void {
    const limit = $combinationIdSelect.data('minimum-results-for-search');

    $combinationIdSelect.removeClass('d-none');
    $combinationIdSelect.select2({
      minimumResultsForSearch: limit,
      ajax: {
        url: () => this.router.generate('admin_products_search_product_combinations', {productId, limit}),
        dataType: 'json',
        type: 'GET',
        delay: 250,
        data(params: Record<string, string>): Record<string, string> {
          return {
            q: params.term,
          };
        },
        processResults(data: ProductCombinationsResult): jQuerySelect2Results {
          // prepend the "all combinations" choice to the top of the list
          const allCombinationsChoice: CombinationChoice = {
            combinationId: Number($combinationIdSelect.data('allCombinationsValue')),
            combinationName: String($combinationIdSelect.data('allCombinationsLabel')),
          };
          const results = <jQuerySelect2Choice[]> [{
            id: allCombinationsChoice.combinationId,
            text: allCombinationsChoice.combinationName,
          }];

          results.push(...data.combinations.map((combination: CombinationChoice) => ({
            id: combination.combinationId,
            text: combination.combinationName,
          })));

          return {results};
        },
      },
    });
  }
}
