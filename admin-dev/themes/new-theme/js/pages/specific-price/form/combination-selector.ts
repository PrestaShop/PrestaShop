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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import Router from '@components/router';
import SpecificPriceMap from '@pages/specific-price/specific-price-map';

const {$} = window;

export default class CombinationSelector {
  readonly productId: number;

  private router: Router;

  private container: HTMLElement;

  constructor(
    router: Router,
    productId: number,
  ) {
    this.router = router;
    this.productId = productId;
    this.container = <HTMLElement>document.querySelector(SpecificPriceMap.formContainer);
    this.initComponent();
  }

  initComponent(): void {
    $(SpecificPriceMap.combinationIdSelect).select2({
      minimumResultsForSearch: 3,
      ajax: {
        url: this.router.generate('admin_products_v2_search_product_combinations', {
          productId: this.productId,
        }),
        dataType: 'json',
        type: 'GET',
        delay: 250,
        data(params: any) {
          return {
            q: params.term,
          };
        },
        processResults(data: any) {
          return {
            results: data.map((item: any) => ({id: item.combinationId, text: item.combinationName})),
          };
        },
      },
    });
  }
}
