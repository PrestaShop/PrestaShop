/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {transform} from '../../js/app/utils/number-comma-transformer';

describe('NumberCommaTransformer', () => {
  describe('transform', () => {
    const assertions = [
      ['12', '12'],
      ['-12', '-12'],
      ['-12,20', '-12.20'],
      ['-12,,20', '-12.20'],
      ['-----12,20', '12.20'],
      ['----12,20', '12.20'],
      ['12alizdjalzjdf20', '12.20'],
      ['-12alizdjalzjdf20', '-12.20'],
      ['12345.678', '12345.678'],
      ['12345dd.dd678', '12345.678'],
      ['12...40', '12.40'],
      ['12,,', '12.'],
      ['1.000,2', '1000.2'],
      ['1N000,2', '1000.2'],
      ['100,2', '100.2'],
      ['1,000,2', '10002'],
      ['1,000,200.5', '1000200.5'],
      ['100,002', '100.002'],
    ];

    assertions.forEach((assertion) => {
      it(`test ${assertion[0]}`, () => {
        expect(transform(assertion[0])).to.eql(assertion[1]);
      });
    });
  });
});
