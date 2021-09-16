/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import {expect} from 'chai';
import {calculateTax} from '@pages/product/edit/helpers/product-form';
import BigNumber from 'bignumber.js';

describe('Product Price Calculation', () => {
  describe('calculateTax', () => {
    const assertions = [
      ['24.000', new BigNumber(1.24)],
      ['10.000', new BigNumber(1.1)],
      ['5.5', new BigNumber(1.055)],
      ['20.000', new BigNumber(1.200)],
      ['2.10', new BigNumber(1.021)],
    ];

    assertions.forEach((assertion) => {
      it(`test ${assertion[0]} should return ${assertion[1]}`, () => {
        expect(calculateTax(assertion[0])).to.eql(assertion[1]);
      });
    });
  });
});
