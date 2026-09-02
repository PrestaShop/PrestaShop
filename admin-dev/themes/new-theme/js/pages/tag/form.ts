/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import TagMap from '@pages/tag/tag-map';
import ProductSearchInput from '@components/form/product-search-input';

const {$} = window;

$(() => {
  new ProductSearchInput(TagMap.productSearchInput);
});
