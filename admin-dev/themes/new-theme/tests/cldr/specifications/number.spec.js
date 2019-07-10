import {expect} from 'chai';
import NumberSpecification from '../../../js/app/cldr/specifications/number';
import NumberSymbol from '../../../js/app/cldr/number-symbol';

describe('NumberSpecification', () => {
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
        new NumberSpecification();
      }).to.throw('Invalid positivePattern');
    });

    it('should throw if invalid negative pattern', () => {
      expect(() => {
        new NumberSpecification(
          '#,##0.###',
        );
      }).to.throw('Invalid negativePattern');
    });

    it('should throw if invalid symbol', () => {
      expect(() => {
        new NumberSpecification(
          '#,##0.###',
          '-#,##0.###',
        );
      }).to.throw('Invalid symbol');
    });

    it('should throw if invalid maxFractionDigits', () => {
      expect(() => {
        new NumberSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
        );
      }).to.throw('Invalid maxFractionDigits');
    });

    it('should throw if invalid minFractionDigits', () => {
      expect(() => {
        new NumberSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
        );
      }).to.throw('Invalid minFractionDigits');
    });

    it('should throw if invalid groupingUsed', () => {
      expect(() => {
        new NumberSpecification(
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
        new NumberSpecification(
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
        new NumberSpecification(
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

    it('should not throw if everything is ok', () => {
      expect(() => {
        new NumberSpecification(
          '#,##0.###',
          '-#,##0.###',
          symbol,
          3,
          0,
          true,
          3,
          3,
        );
      }).to.not.throw();
    });
  });
});
