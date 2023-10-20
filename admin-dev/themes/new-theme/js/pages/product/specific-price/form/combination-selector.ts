/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import Router from '@components/router';
import SpecificPriceMap from '@pages/product/specific-price/specific-price-map';

const {$} = window;

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

export default class CombinationSelector {
  readonly productId: number;

  private router: Router;

  private container: HTMLElement;

  private shopId: number|null;

  constructor(
    router: Router,
    productId: number,
  ) {
    this.router = router;
    this.productId = productId;
    this.container = <HTMLElement>document.querySelector(SpecificPriceMap.formContainer);
    this.shopId = null;
    this.initComponent();
  }

  async initComponent(): Promise<void> {
    const $combinationIdSelect = $(SpecificPriceMap.combinationIdSelect);

    // inside select2 callback "this" is the jquery component,
    // so we need to assign a var to reach actual "this" (the combinationSelector) inside the callback
    const self = this;
    const limit = $combinationIdSelect.data('minimum-results-for-search');

    $combinationIdSelect.select2({
      minimumResultsForSearch: limit,
      ajax: {
        url: () => this.getUrl(limit),
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
          const allCombinationsChoice = <CombinationChoice> self.getChoiceForAllCombinations();
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

  getUrl(limit: number): string {
    const routeParams = <Record<string, number>> {
      productId: this.productId,
      limit,
    };

    const shopIdSelect = <HTMLSelectElement> this.container.querySelector(SpecificPriceMap.shopIdSelect);
    let shopId = null;

    // This check is here for when the multishop is not enabled.
    // The selector shopIdSelect does not exist when multishop is not enabled.
    if (shopIdSelect !== null) {
      shopId = Number(shopIdSelect.value);
    }

    if (shopId) {
      routeParams.shopId = shopId;
    }

    return this.router.generate('admin_products_search_product_combinations', routeParams);
  }

  getChoiceForAllCombinations(): CombinationChoice {
    const combinationIdSelect = <HTMLSelectElement> this.container.querySelector(SpecificPriceMap.combinationIdSelect);

    return {
      combinationId: Number(combinationIdSelect.dataset.allCombinationsValue),
      combinationName: String(combinationIdSelect.dataset.allCombinationsLabel),
    };
  }
}
