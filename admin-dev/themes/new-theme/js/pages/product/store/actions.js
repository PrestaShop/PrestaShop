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
import * as taxCalculations from '../tax-calculations';

export const updatePriceTaxExcluded = ({commit}, payload) => {
  commit(types.SET_PRICE_TAX_EXCLUDED, payload.priceTaxExcluded);
  commit(types.SET_PRICE_TAX_INCLUDED, taxCalculations.includeTaxes(payload.priceTaxExcluded, payload.taxRule.rate));
};

export const updatePriceTaxIncluded = ({commit}, payload) => {
  commit(types.SET_PRICE_TAX_INCLUDED, payload.priceTaxIncluded);
  commit(types.SET_PRICE_TAX_EXCLUDED, taxCalculations.excludeTaxes(payload.priceTaxIncluded, payload.taxRule.rate));
};

export const updateTaxRule = ({commit}, payload) => {
  commit(types.SET_TAX_RULE, payload.taxRule);
  commit(types.SET_PRICE_TAX_INCLUDED, taxCalculations.includeTaxes(payload.priceTaxExcluded, payload.taxRule.rate));
};
