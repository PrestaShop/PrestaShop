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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
import {expect} from 'chai';
import {transform} from '../../js/app/utils/number-comma-transformer';

describe('NumberCommaTransformer', () => {
  describe('transform', () => {
    const assertions = [
      ['12', '12'],
      ['-12', '-12'],
      ['-12,20', '-12.20'],
      ['-12,,20', '-12.20'],
      ['-----12,20', '12.20'],
      ['----12,20', '12.20'],
      ['12alizdjalzjdf20', '12.20'],
      ['-12alizdjalzjdf20', '-12.20'],
      ['12345.678', '12345.678'],
      ['12345dd.dd678', '12345.678'],
      ['12...40', '12.40'],
      ['12,,', '12.'],
      ['1.000,2', '1000.2'],
      ['1N000,2', '1000.2'],
      ['100,2', '100.2'],
      ['1,000,2', '10002'],
      ['1,000,200.5', '1000200.5'],
      ['100,002', '100.002'],
    ];

    assertions.forEach((assertion) => {
      it(`test ${assertion[0]}`, () => {
        expect(transform(assertion[0])).to.eql(assertion[1]);
      });
    });
  });
});
