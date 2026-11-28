/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import { expect } from 'chai';
import stockQueryParams from '../../../js/app/pages/stock/store/query-params';

describe('stockQueryParams', () => {
  it('uses the names the stock API accepts for suppliers and categories', () => {
    const params = stockQueryParams({ suppliers: [3, 7], categories: [12] });

    expect(params.getAll('supplier_id[]')).to.deep.equal(['3', '7']);
    expect(params.getAll('category_id[]')).to.deep.equal(['12']);
    expect(params.has('suppliers')).to.equal(false);
    expect(params.has('categories')).to.equal(false);
  });

  it('keeps the paging, ordering and search parameters', () => {
    const params = stockQueryParams({
      order: 'product',
      page_size: 30,
      page_index: 2,
      keywords: 'mug',
      active: '1',
      low_stock: 1,
    });

    expect(params.get('order')).to.equal('product');
    expect(params.get('page_size')).to.equal('30');
    expect(params.get('page_index')).to.equal('2');
    expect(params.get('keywords')).to.equal('mug');
    expect(params.get('active')).to.equal('1');
    expect(params.get('low_stock')).to.equal('1');
  });

  it('omits empty values instead of sending them', () => {
    const params = stockQueryParams({
      order: 'product',
      keywords: '',
      active: null,
      suppliers: [],
      categories: undefined,
    });

    expect(params.get('order')).to.equal('product');
    expect(params.has('keywords')).to.equal(false);
    expect(params.has('active')).to.equal(false);
    expect(params.has('supplier_id[]')).to.equal(false);
    expect(params.has('category_id[]')).to.equal(false);
  });

  it('returns nothing for an empty filter set', () => {
    expect(stockQueryParams({}).toString()).to.equal('');
  });
});
