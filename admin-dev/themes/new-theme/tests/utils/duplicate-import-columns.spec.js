/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {
  describeDuplicateImportColumns,
  findDuplicateImportColumns,
} from '../../js/app/utils/duplicate-import-columns';

const column = (position, value, label) => ({column: position, value, label});

describe('DuplicateImportColumns', () => {
  describe('findDuplicateImportColumns', () => {
    it('finds nothing when every column has its own type', () => {
      expect(findDuplicateImportColumns([
        column(1, 'id', 'ID'),
        column(2, 'name', 'Name'),
      ])).to.deep.equal([]);
    });

    it('groups the columns sharing a type', () => {
      const groups = findDuplicateImportColumns([
        column(1, 'id', 'ID'),
        column(2, 'price', 'Price'),
        column(3, 'price', 'Price'),
      ]);

      expect(groups).to.have.lengthOf(1);
      expect(groups[0].map((selection) => selection.column)).to.deep.equal([2, 3]);
    });

    it('reports every group, not just the first', () => {
      const groups = findDuplicateImportColumns([
        column(1, 'price', 'Price'),
        column(2, 'reference', 'Reference'),
        column(3, 'price', 'Price'),
        column(4, 'reference', 'Reference'),
      ]);

      expect(groups).to.have.lengthOf(2);
    });

    it('keeps a group of three together instead of splitting it in pairs', () => {
      const groups = findDuplicateImportColumns([
        column(1, 'price', 'Price'),
        column(2, 'price', 'Price'),
        column(3, 'price', 'Price'),
      ]);

      expect(groups).to.have.lengthOf(1);
      expect(groups[0].map((selection) => selection.column)).to.deep.equal([1, 2, 3]);
    });
  });

  describe('describeDuplicateImportColumns', () => {
    it('says nothing when there is nothing to say', () => {
      expect(describeDuplicateImportColumns([
        column(1, 'id', 'ID'),
      ])).to.equal('');
    });

    it('names the type and every column it was chosen for', () => {
      expect(describeDuplicateImportColumns([
        column(1, 'id', 'ID'),
        column(2, 'price', 'Price tax excluded'),
        column(7, 'price', 'Price tax excluded'),
      ])).to.equal('Price tax excluded (2, 7)');
    });

    it('separates several duplicated types', () => {
      expect(describeDuplicateImportColumns([
        column(2, 'price', 'Price'),
        column(3, 'reference', 'Reference'),
        column(7, 'price', 'Price'),
        column(9, 'reference', 'Reference'),
      ])).to.equal('Price (2, 7); Reference (3, 9)');
    });
  });
});
