/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
/* eslint-disable max-len */
import Router from '@components/router';
import {AttributeGroup} from '@pages/product/combination/types';

const router = new Router();
const {$} = window;

export const getProductAttributeGroups = async (productId: number, shopId: number|null): Promise<Array<AttributeGroup>> => {
  const routeParams = <Record<string, number>> {productId};

  if (shopId) {
    routeParams.shopId = shopId;
  }

  return $.get(router.generate('admin_products_attribute_groups', routeParams));
};

export const getAllAttributeGroups = async (shopId: number|null): Promise<Array<AttributeGroup>> => $.get(router.generate(
  'admin_all_attribute_groups',
  shopId ? {shopId} : {},
));

export default {
  getProductAttributeGroups,
  getAllAttributeGroups,
};
