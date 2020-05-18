/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import * as types from './mutation-types';

export const updatePriceTaxExcluded = (state, price) => {
  //@todo: lib for calculations;  like Big.js
  state.commit(types.SET_PRICE_TAX_EXCLUDED, price);

  //calculate and commit price included here.@todo: random value for test
    state.commit(types.SET_PRICE_TAX_INCLUDED, price - 500);
    debugger;
};

export const updatePriceTaxIncluded = (state, price) => {
  state.commit(types.SET_PRICE_TAX_INCLUDED, price);
  //calculate and commit price excluded here.@todo: random value for test
    state.commit(types.SET_PRICE_TAX_EXCLUDED, price + 500);
    debugger;
};
