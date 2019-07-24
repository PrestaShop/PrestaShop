import {expect} from 'chai';
import PriceSpecification from '../../../js/app/cldr/specifications/price';
import NumberSymbol from '../../../js/app/cldr/number-symbol';

describe('PriceSpecification', () => {
  let symbol;
  beforeEach(() => {
    symbol = new NumberSymbol(
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
      'NaN',
    );
  });
  describe('validateData', () => {
    it('should throw if invalid positive pattern', () => {
      expect(() => {
        new PriceSpecification();
      }).to.throw('Invalid positivePattern');
    });

    it('should throw if invalid negative pattern', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
        );
      }).to.throw('Invalid negativePattern');
    });

    it('should throw if invalid symbol', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
        );
      }).to.throw('Invalid symbol');
    });

    it('should throw if invalid maxFractionDigits', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
        );
      }).to.throw('Invalid maxFractionDigits');
    });

    it('should throw if invalid minFractionDigits', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
        );
      }).to.throw('Invalid minFractionDigits');
    });

    it('should throw if invalid groupingUsed', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
        );
      }).to.throw('Invalid groupingUsed');
    });

    it('should throw if invalid primaryGroupSize', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          false,
        );
      }).to.throw('Invalid primaryGroupSize');
    });

    it('should throw if invalid secondaryGroupSize', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          true,
          3,
        );
      }).to.throw('Invalid secondaryGroupSize');
    });

    it('should throw if invalid currencySymbol', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          true,
          3,
          3,
        );
      }).to.throw('Invalid currencySymbol');
    });

    it('should throw if invalid currencyCode', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          true,
          3,
          3,
          '$',
        );
      }).to.throw('Invalid currencyCode');
    });

    it('should not throw if everything is ok', () => {
      expect(() => {
        new PriceSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          true,
          3,
          3,
          '$',
          'USD',
        );
      }).to.not.throw();
    });
  });
});
