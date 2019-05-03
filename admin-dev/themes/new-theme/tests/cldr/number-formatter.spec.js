import {expect} from 'chai';
import NumberFormatter from '../../js/app/cldr/number-formatter';
import PriceSpecification from '../../js/app/cldr/specifications/price';
import NumberSymbol from '../../js/app/cldr/number-symbol';

describe('NumberFormatter', () => {
  let currency;
  beforeEach(() => {
    const symbol = new NumberSymbol(
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
    currency = new NumberFormatter(
      new PriceSpecification(
        '¤#,##0.###',
        '-¤#,##0.###',
        symbol,
        3,
        0,
        true,
        3,
        3,
        '$',
        'USD',
      ),
    );
  });

  describe('extractMajorMinorDigits', () => {
    const assertions = [
      [10, ['10', '']],
      [10.1, ['10', '1']],
      [11.12345, ['11', '12345']],
      [11.00000, ['11', '']],
    ];
    assertions.forEach((assertion) => {
      it(`test ${assertion[0]}`, () => {
        expect(currency.extractMajorMinorDigits(assertion[0])).to.eql(assertion[1]);
      });
    });
  });

  describe('getCldrPattern', () => {
    const assertions = [
      [false, '¤#,##0.###'],
      [true, '-¤#,##0.###'],
    ];
    assertions.forEach((assertion) => {
      it(`test isNegative ${assertion[0]}`, () => {
        expect(currency.getCldrPattern(assertion[0])).to.eq(assertion[1]);
      });
    });
  });

  describe('splitMajorGroups', () => {
    const assertions = [
      ['10', '10'],
      ['100', '100'],
      ['1000', '1,000'],
      ['10000', '10,000'],
      ['100000', '100,000'],
      ['1000000', '1,000,000'],
      ['10000000', '10,000,000'],
      ['100000000', '100,000,000'],
    ];
    assertions.forEach((assertion) => {
      it(`test ${assertion[0]} should display ${assertion[1]}`, () => {
        expect(currency.splitMajorGroups(assertion[0])).to.eq(assertion[1]);
      });
    });
  });

  describe('adjustMinorDigitsZeroes', () => {
    const assertions = [
      ['10000', '10'],
      ['100', '100'],
      ['12', '12'],
      ['120', '120'],
      ['1271', '1271'],
      ['1270', '127'],
    ];
    assertions.forEach((assertion) => {
      it(`test ${assertion[0]} should display ${assertion[1]}`, () => {
        currency.numberSpecification.minFractionDigits = 2;
        expect(currency.adjustMinorDigitsZeroes(assertion[0])).to.eq(assertion[1]);
      });
    });
  });

  describe('addPlaceholders', () => {
    const assertions = [
      ['100,000.13', '¤#,##0.00', '¤100,000.13'],
      ['100.13', '¤#,##0.00', '¤100.13'],
    ];
    assertions.forEach((assertion) => {
      it(`test ${assertion[0]} with pattern ${assertion[1]} should display ${assertion[2]}`, () => {
        expect(currency.addPlaceholders(assertion[0], assertion[1])).to.eq(assertion[2]);
      });
    });
  });

  describe('replaceSymbols', () => {
    it('should replace all symbols', () => {
      currency.numberSpecification.symbol = new NumberSymbol(
        '_',
        ':)',
        ';',
        '%',
        'Moins',
        '+',
        'E',
        '×',
        '‰',
        '∞',
        'NaN',
      );
      expect(currency.replaceSymbols('¤-10,000,000.13')).to.eq('¤Moins10:)000:)000_13');
    });
  });

  describe('addPlaceholders', () => {
    it('should replace currency symbol', () => {
      expect(currency.performSpecificReplacements('¤10,000,000.13')).to.eq('$10,000,000.13');
    });
  });

  describe('format', () => {
    const assertions = [
      ['10.3', '$10.300'],
      ['100.34', '$100.340'],
      ['1000.345', '$1,000.345'],
      ['10000.3456', '$10,000.346'],
      ['100000.512', '$100,000.512'],
      ['1000000', '$1,000,000.000'],
      ['10000000', '$10,000,000.000'],
      ['100000000', '$100,000,000.000'],
    ];
    assertions.forEach((assertion) => {
      it(`test ${assertion[0]} should display ${assertion[1]}`, () => {
        expect(currency.format(assertion[0])).to.eq(assertion[1]);
      });
    });
  });
});
