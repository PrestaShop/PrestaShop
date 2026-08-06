/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {
  parseFormat,
  serializeLines,
  resolveToken,
  preferredRaw,
  renderPreview,
  missingRequired,
  placedFieldKeys,
} from '../../../js/pages/country/components/addressFormatModel';

const sampleData = {
  Customer: {firstname: 'John', lastname: 'DOE', company: 'Acme Ltd.'},
  Country: {name: 'France'},
  Address: {
    firstname: 'John',
    lastname: 'DOE',
    address1: '16 Main street',
    postcode: '75002',
    city: 'Paris',
  },
};

describe('addressFormatModel', () => {
  describe('resolveToken', () => {
    it('resolves explicit Object:field', () => {
      const t = resolveToken('Country:name');
      expect(t.object).to.equal('Country');
      expect(t.field).to.equal('name');
      expect(t.raw).to.equal('Country:name');
    });

    it('resolves bare tokens to Address (legacy parser semantics)', () => {
      expect(resolveToken('firstname').object).to.equal('Address');
      expect(resolveToken('city').object).to.equal('Address');
      expect(resolveToken('phone_mobile').object).to.equal('Address');
    });
  });

  describe('parseFormat / serializeLines round-trip', () => {
    it('preserves bare tokens (no re-prefixing)', () => {
      const input = 'firstname lastname\naddress1\npostcode city\nCountry:name';
      const lines = parseFormat(input);
      expect(serializeLines(lines)).to.equal(input);
    });

    it('preserves prefixed tokens', () => {
      const input = 'Customer:firstname Customer:lastname\nAddress:city';
      const lines = parseFormat(input);
      expect(serializeLines(lines)).to.equal(input);
    });

    it('normalizes mixed whitespace inside a line', () => {
      const input = 'firstname    lastname';
      const lines = parseFormat(input);
      expect(serializeLines(lines)).to.equal('firstname lastname');
    });

    it('returns an empty array for an empty string', () => {
      expect(parseFormat('')).to.eql([]);
    });
  });

  describe('preferredRaw', () => {
    it('emits the bare form for Address-tab fields', () => {
      expect(preferredRaw('Address', 'firstname')).to.equal('firstname');
      expect(preferredRaw('Address', 'city')).to.equal('city');
    });

    it('emits Object:field for non-Address tabs', () => {
      expect(preferredRaw('Country', 'name')).to.equal('Country:name');
      expect(preferredRaw('Customer', 'firstname')).to.equal('Customer:firstname');
      expect(preferredRaw('State', 'iso_code')).to.equal('State:iso_code');
    });
  });

  describe('renderPreview', () => {
    it('substitutes tokens with sample values, joining with spaces', () => {
      const lines = parseFormat('firstname lastname\naddress1\nCountry:name');
      const preview = renderPreview(lines, sampleData);
      expect(preview).to.eql(['John DOE', '16 Main street', 'France']);
    });

    it('skips lines whose tokens all resolve to empty values', () => {
      const lines = parseFormat('Customer:siret\nCountry:name');
      const preview = renderPreview(lines, sampleData);
      // Customer:siret has no sample value → its line is skipped.
      expect(preview).to.eql(['France']);
    });
  });

  describe('missingRequired', () => {
    const required = ['firstname', 'lastname', 'address1', 'city', 'Country:name'];

    it('returns empty when all required are placed (bare form)', () => {
      const lines = parseFormat('firstname lastname\naddress1 city\nCountry:name');
      expect(missingRequired(lines, required)).to.eql([]);
    });

    it('flags a missing bare token', () => {
      const lines = parseFormat('lastname\naddress1\ncity\nCountry:name');
      expect(missingRequired(lines, required)).to.eql(['firstname']);
    });

    it('flags a missing prefixed token', () => {
      const lines = parseFormat('firstname lastname\naddress1\ncity');
      expect(missingRequired(lines, required)).to.eql(['Country:name']);
    });

    it('does NOT count Customer:firstname as satisfying bare firstname', () => {
      // bare `firstname` requires Address:firstname; Customer:firstname is a different token.
      const lines = parseFormat('Customer:firstname lastname\naddress1\ncity\nCountry:name');
      expect(missingRequired(lines, required)).to.eql(['firstname']);
    });

    it('counts Address:firstname as satisfying bare firstname', () => {
      const lines = parseFormat('Address:firstname lastname\naddress1\ncity\nCountry:name');
      expect(missingRequired(lines, required)).to.eql([]);
    });
  });

  describe('placedFieldKeys', () => {
    it('returns Object:field keys for every placed token', () => {
      const lines = parseFormat('firstname\nCountry:name');
      const keys = placedFieldKeys(lines);
      expect(keys.has('Address:firstname')).to.equal(true);
      expect(keys.has('Country:name')).to.equal(true);
      expect(keys.size).to.equal(2);
    });
  });
});
