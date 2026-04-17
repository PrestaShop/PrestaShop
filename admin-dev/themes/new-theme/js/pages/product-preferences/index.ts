/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import StockManagementOptionHandler from '@pages/product-preferences/stock-management-option-handler';
import CatalogModeOptionHandler from '@pages/product-preferences/catalog-mode-option-handler';
import * as pageMap from '@pages/product-preferences/product-preferences-page-map';

const {$} = window;

$(() => {
  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
  new StockManagementOptionHandler();
  new CatalogModeOptionHandler(pageMap);
});
