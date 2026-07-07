/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {
  parseTail,
  quoteValue,
  serializeTail,
  tokenize,
  unquoteValue,
} from '../../../js/pages/extra-property-definition/form/grammar/constraint-dsl';

// The tail lexer backing the constraint builder's typed option editor. The splitting rules are
// pinned to ExtraPropertyConstraintMapper (quotes/escapes/bracket depth) — the server stays the
// parsing/validation authority, rows are validated on submit.
describe('extra property constraint tail lexer', () => {
  describe('tokenize', () => {
    it('splits on top-level commas and newlines with 1-based starting lines', () => {
      expect(tokenize('NotBlank\nLength(min: 2, max: 64)\n\nAll[Url, NotBlank]')).to.deep.equal([
        {token: 'NotBlank', line: 1},
        {token: 'Length(min: 2, max: 64)', line: 2},
        {token: 'All[Url, NotBlank]', line: 4},
      ]);
    });

    it('never splits inside quotes, with backslash escapes honored', () => {
      expect(tokenize("Choice(['a,b', 'c\\'d'])")).to.deep.equal([
        {token: "Choice(['a,b', 'c\\'d'])", line: 1},
      ]);
    });

    it('never splits inside brackets or parentheses', () => {
      expect(tokenize('min: [1, 2], max: (3)', ',')).to.deep.equal([
        {token: 'min: [1, 2]', line: 1},
        {token: 'max: (3)', line: 1},
      ]);
    });
  });

  describe('parseTail / serializeTail', () => {
    it('lexes named options', () => {
      expect(parseTail('min: 2, max: 64')).to.deep.equal([
        {key: 'min', value: '2'},
        {key: 'max', value: '64'},
      ]);
    });

    it('lexes a positional value', () => {
      expect(parseTail("'generic_name'")).to.deep.equal([{key: null, value: "'generic_name'"}]);
    });

    it('keeps bracketed lists as one positional value', () => {
      expect(parseTail("['a', 'b,c']")).to.deep.equal([{key: null, value: "['a', 'b,c']"}]);
    });

    it('lexes an empty tail to no options', () => {
      expect(parseTail('  ')).to.deep.equal([]);
    });

    it('rejects tails it cannot represent', () => {
      expect(parseTail('min: 2, weird!')).to.equal(null);
    });

    it('serializes back, skipping blank values', () => {
      expect(serializeTail([{key: 'min', value: '2'}, {key: 'max', value: ''}])).to.equal('min: 2');
      expect(serializeTail([{key: null, value: '5'}])).to.equal('5');
    });

    it('round-trips a lexed tail identically', () => {
      const tail = "min: 2, max: 64, charset: 'UTF-8'";

      expect(serializeTail(parseTail(tail))).to.equal(tail);
    });
  });

  describe('value quoting', () => {
    it('unquotes with escapes honored', () => {
      expect(unquoteValue("'it\\'s'")).to.equal("it's");
      expect(unquoteValue('plain')).to.equal('plain');
    });

    it('quotes with escapes (inverse of unquote)', () => {
      expect(unquoteValue(quoteValue("it's a \\ test"))).to.equal("it's a \\ test");
    });
  });

  // PARITY FIXTURES — mirror of ExtraPropertyConstraintMapperTest::tailParityProvider() (PHP).
  // The same tails are asserted against the server-side mapper: a quoting/splitting rule changed
  // on one side without the other makes the sibling suite fail, surfacing the drift in CI.
  describe('parity fixtures with the PHP mapper', () => {
    const QUOTING_PARITY = [
      {tail: "'simple'", decoded: 'simple'},
      {tail: '"double"', decoded: 'double'},
      {tail: "'it\\'s'", decoded: "it's"},
      {tail: '"she said \\"hi\\""', decoded: 'she said "hi"'},
      {tail: "'back\\\\slash'", decoded: 'back\\slash'},
      {tail: "'a, b'", decoded: 'a, b'},
      {tail: "'with (parens) and [brackets]'", decoded: 'with (parens) and [brackets]'},
      {tail: 'unquoted_text', decoded: 'unquoted_text'},
    ];

    QUOTING_PARITY.forEach(({tail, decoded}) => {
      it(`lexes and unquotes ${tail} like the mapper`, () => {
        const options = parseTail(tail);

        expect(options).to.have.lengthOf(1);
        expect(options[0].key).to.equal(null);
        expect(unquoteValue(options[0].value)).to.equal(decoded);
        expect(serializeTail(options)).to.equal(tail);
      });
    });

    it('splits named options like the mapper', () => {
      expect(parseTail('min: 2, max: 64')).to.deep.equal([
        {key: 'min', value: '2'},
        {key: 'max', value: '64'},
      ]);
      expect(parseTail("choices: ['a', 'b,c'], multiple: true")).to.deep.equal([
        {key: 'choices', value: "['a', 'b,c']"},
        {key: 'multiple', value: 'true'},
      ]);
    });
  });
});
