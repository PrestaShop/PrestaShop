/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import { expect } from 'chai';
import { sortPrefixMatchesFirst } from '../../js/app/utils/select2-prefix-first';

// The order statuses of a stock shop, in the order the shop declares them. Typing "s" matches five of
// them with select2's substring filter, and only one of them starts with it.
const orderStatuses = () => [
  { text: 'Awaiting check payment' },
  { text: 'Payment accepted' },
  { text: 'Processing in progress' },
  { text: 'Shipped' },
  { text: 'Delivered' },
  { text: 'Canceled' },
  { text: 'Refunded' },
  { text: 'Payment error' },
  { text: 'On backorder (paid)' },
];

describe('sortPrefixMatchesFirst', () => {
  it('puts the status that starts with the letter first', () => {
    const sorted = sortPrefixMatchesFirst('s', orderStatuses());

    expect(sorted[0].text).to.equal('Shipped');
  });

  it('keeps the declared order among the results that only contain the term', () => {
    const sorted = sortPrefixMatchesFirst('s', orderStatuses()).map((result) => result.text);

    expect(sorted).to.deep.equal([
      'Shipped',
      'Awaiting check payment',
      'Payment accepted',
      'Processing in progress',
      'Delivered',
      'Canceled',
      'Refunded',
      'Payment error',
      'On backorder (paid)',
    ]);
  });

  it('ignores the case of what was typed', () => {
    expect(sortPrefixMatchesFirst('SHIP', orderStatuses())[0].text).to.equal('Shipped');
    expect(sortPrefixMatchesFirst('ship', orderStatuses())[0].text).to.equal('Shipped');
  });

  it('changes nothing when nothing was typed', () => {
    const original = orderStatuses().map((result) => result.text);

    expect(sortPrefixMatchesFirst('', orderStatuses()).map((r) => r.text)).to.deep.equal(original);
    expect(sortPrefixMatchesFirst('   ', orderStatuses()).map((r) => r.text)).to.deep.equal(original);
  });

  it('trims what was typed, because select2 does not', () => {
    expect(sortPrefixMatchesFirst('  ship  ', orderStatuses())[0].text).to.equal('Shipped');
  });

  it('promotes several matches and keeps them in their declared order', () => {
    const sorted = sortPrefixMatchesFirst('pay', orderStatuses()).map((r) => r.text);

    expect(sorted.slice(0, 2)).to.deep.equal(['Payment accepted', 'Payment error']);
  });

  it('ranks inside a group without moving the group', () => {
    const grouped = [
      { text: 'Paid', children: [{ text: 'Awaiting payment' }, { text: 'Payment accepted' }] },
      { text: 'Sent', children: [{ text: 'Processing in progress' }, { text: 'Shipped' }] },
    ];

    const sorted = sortPrefixMatchesFirst('s', grouped);

    expect(sorted.map((group) => group.text)).to.deep.equal(['Paid', 'Sent']);
    expect(sorted[1].children.map((child) => child.text)).to.deep.equal([
      'Shipped',
      'Processing in progress',
    ]);
  });

  it('does not mutate what select2 passed in', () => {
    const results = orderStatuses();

    sortPrefixMatchesFirst('s', results);

    expect(results.map((r) => r.text)).to.deep.equal(orderStatuses().map((r) => r.text));
  });

  it('leaves an entry with no text alone rather than throwing', () => {
    const sorted = sortPrefixMatchesFirst('s', [{ id: 1 }, { text: 'Shipped' }]);

    expect(sorted[0].text).to.equal('Shipped');
  });
});
