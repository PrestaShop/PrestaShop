/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import IframeClient from '@components/modal/iframe-client';
import ProductMap from '@pages/product/product-map';
import ProductEventMap from '@pages/product/product-event-map';

$(() => {
  window.prestashop.component.initComponents([
    'ShopSelector',
    'IframeClient',
  ]);

  // eslint-disable-next-line
  const iframeClient: IframeClient = window.prestashop.instance.iframeClient;
  document.querySelector<HTMLElement>(ProductMap.shops.cancelButton)?.addEventListener('click', () => {
    iframeClient.dispatchEvent(ProductEventMap.cancelProductShops);
  });
});
