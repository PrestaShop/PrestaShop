/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';

const router = new Router();
const {$} = window;

export const deleteSpecificPrice = async (specificPriceId: string): Promise<JQuery.jqXHR> => $.ajax({
  url: router.generate('admin_products_specific_prices_delete', {
    specificPriceId,
  }),
  type: 'DELETE',
});

export default {
  deleteSpecificPrice,
};
