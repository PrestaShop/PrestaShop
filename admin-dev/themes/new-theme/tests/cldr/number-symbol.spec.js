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
