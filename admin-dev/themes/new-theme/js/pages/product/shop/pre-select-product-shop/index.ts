/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ProductMap from '@pages/product/product-map';
import selectShopForEdition from '@pages/product/shop/select-shop-modal';

document.addEventListener('DOMContentLoaded', () => {
  const warning = document.querySelector<HTMLElement>(ProductMap.shops.contextWarning);

  if (warning) {
    selectShopForEdition(warning, warning.dataset?.shopIds?.split(',') ?? []);
  }
});
