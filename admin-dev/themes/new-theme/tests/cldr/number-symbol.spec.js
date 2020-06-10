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
import {expect} from 'chai';
import NumberSymbol from '../../js/app/cldr/number-symbol';

describe('NumberSymbol', () => {
  describe('validateData', () => {
    it('should throw if invalid decimal', () => {
      expect(() => { new NumberSymbol(); }).to.throw('Invalid decimal');
    });

    it('should throw if invalid group', () => {
      expect(() => {
        new NumberSymbol(
          '.',
        );
      }).to.throw('Invalid group');
    });

    it('should throw if invalid symbol list', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
        );
      }).to.throw('Invalid symbol list');
    });

    it('should throw if invalid percentSign', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
        );
      }).to.throw('Invalid percentSign');
    });

    it('should throw if invalid minusSign', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
        );
      }).to.throw('Invalid minusSign');
    });

    it('should throw if invalid plusSign', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
        );
      }).to.throw('Invalid plusSign');
    });

    it('should throw if invalid exponential', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
          '+',
        );
      }).to.throw('Invalid exponential');
    });

    it('should throw if invalid superscriptingExponent', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
          '+',
          'E',
        );
      }).to.throw('Invalid superscriptingExponent');
    });

    it('should throw if invalid perMille', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
          '+',
          'E',
          '×',
        );
      }).to.throw('Invalid perMille');
    });

    it('should throw if invalid infinity', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
          '+',
          'E',
          '×',
          '‰',
        );
      }).to.throw('Invalid infinity');
    });

    it('should throw if invalid nan', () => {
      expect(() => {
        new NumberSymbol(
          '.',
          ',',
          ';',
          '%',
          '-',
          '+',
          'E',
          '×',
          '‰',
          '∞',
        );
      }).to.throw('Invalid nan');
    });
  });
});
