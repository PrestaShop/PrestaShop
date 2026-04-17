/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/* eslint-disable max-len */

import Router from '@components/router';
import {TreeCategory} from '@pages/product/category/types';

const router = new Router();
const {$} = window;

export const getCategories = async (): Promise<Array<TreeCategory>> => $.get(router.generate('admin_categories_get_categories_tree'));

export default {
  getCategories,
};
