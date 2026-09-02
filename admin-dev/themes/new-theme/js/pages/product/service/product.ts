/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Router from '@components/router';

const router = new Router();

export interface QuantityResult {
  quantity: number,
}

export const getProductQuantity = async (productId: number, shopId: number): Promise<Response> => fetch(
  router.generate('admin_products_quantity', {
    productId,
    shopId,
  }),
);

export default {
  getProductQuantity,
};
