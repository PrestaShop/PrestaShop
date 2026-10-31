/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {isNil, omitBy} from 'lodash';

const isParamInvalid = (value: any) => isNil(value) || value.length <= 0;

/**
 * Turns the page's own filter object into the query string the stock API accepts.
 *
 * The two sets of names differ: the page keeps `suppliers` and `categories`, while
 * QueryStockParamsCollection whitelists `supplier_id` and `category_id` and silently ignores anything
 * else. Building that mapping in more than one place is how the CSV export came to send the page's
 * names and export the whole catalogue, so both the listing and the export read it from here.
 */
export default function stockQueryParams(filters: Record<string, any>): URLSearchParams {
  const params = new URLSearchParams();

  const scalars = omitBy(
    {
      order: filters.order,
      page_size: filters.page_size,
      page_index: filters.page_index,
      keywords: filters.keywords,
      active: filters.active,
      low_stock: filters.low_stock,
    },
    isParamInvalid,
  );

  Object.entries(scalars).forEach(([key, value]) => params.append(key, String(value)));

  (filters.suppliers ?? []).forEach((id: string) => params.append('supplier_id[]', id));
  (filters.categories ?? []).forEach((id: string) => params.append('category_id[]', id));

  return params;
}
