/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import BigNumber from 'bignumber.js';
import UpdateOriginTracker from '../../../js/components/form/update-origin-tracker';

describe('UpdateOriginTracker', () => {
  it('reports no origin outside an update', () => {
    const tracker = new UpdateOriginTracker();

    expect(tracker.isOrigin('impact.priceTaxIncluded')).to.equal(false);
  });

  it('reports the edited field as the origin while it is being updated', () => {
    const tracker = new UpdateOriginTracker();
    let seen = null;

    tracker.run('impact.priceTaxIncluded', () => {
      seen = tracker.isOrigin('impact.priceTaxIncluded');
    });

    expect(seen).to.equal(true);
  });

  it('does not report a field the update only derives', () => {
    const tracker = new UpdateOriginTracker();
    let seen = null;

    tracker.run('impact.priceTaxIncluded', () => {
      seen = tracker.isOrigin('impact.priceTaxExcluded');
    });

    expect(seen).to.equal(false);
  });

  it('keeps the outermost origin when a derived update starts its own cycle', () => {
    // This is the echo: writing the tax excluded price re-enters the handler, which converts it
    // back and would assign the tax included one the merchant just typed.
    const tracker = new UpdateOriginTracker();
    const written = [];

    const write = (modelKey) => {
      if (!tracker.isOrigin(modelKey)) {
        written.push(modelKey);
      }
    };

    tracker.run('impact.priceTaxIncluded', () => {
      write('impact.priceTaxExcluded');

      tracker.run('impact.priceTaxExcluded', () => {
        write('impact.priceTaxIncluded');
      });
    });

    expect(written).to.deep.equal(['impact.priceTaxExcluded']);
  });

  it('releases the origin once the outermost update finishes', () => {
    const tracker = new UpdateOriginTracker();

    tracker.run('impact.priceTaxIncluded', () => {
      tracker.run('impact.priceTaxExcluded', () => {});
    });

    expect(tracker.isOrigin('impact.priceTaxIncluded')).to.equal(false);
    expect(tracker.isOrigin('impact.priceTaxExcluded')).to.equal(false);
  });

  it('releases the origin when the update throws', () => {
    const tracker = new UpdateOriginTracker();

    expect(() => tracker.run('impact.priceTaxIncluded', () => {
      throw new Error('conversion failed');
    })).to.throw('conversion failed');

    expect(tracker.isOrigin('impact.priceTaxIncluded')).to.equal(false);
  });

  describe('the conversion it protects', () => {
    const PRECISION = 6;
    const TAX_RATIO = new BigNumber(1.23);

    // The two halves of the combination form's price conversion, as the model runs them.
    const convert = (modelKey, fields) => (modelKey === 'priceTaxIncluded'
      ? ['priceTaxExcluded', new BigNumber(fields.priceTaxIncluded).dividedBy(TAX_RATIO).toFixed(PRECISION)]
      : ['priceTaxIncluded', new BigNumber(fields.priceTaxExcluded).times(TAX_RATIO).toFixed(PRECISION)]);

    const edit = (tracker, fields, modelKey, typed) => {
      const apply = (key) => {
        const [derivedKey, derivedValue] = convert(key, fields);

        if (tracker && tracker.isOrigin(derivedKey)) {
          return;
        }

        if (fields[derivedKey] === derivedValue) {
          return;
        }

        fields[derivedKey] = derivedValue;
        apply(derivedKey);
      };

      fields[modelKey] = typed;

      if (tracker) {
        tracker.run(modelKey, () => apply(modelKey));
      } else {
        apply(modelKey);
      }
    };

    it('drifts without the tracker, which is the reported defect', () => {
      const fields = {priceTaxIncluded: '0', priceTaxExcluded: '0'};

      edit(null, fields, 'priceTaxIncluded', '12');

      expect(fields.priceTaxIncluded).to.equal('12.000001');
    });

    it('leaves the typed price alone with the tracker', () => {
      const tracker = new UpdateOriginTracker();
      const fields = {priceTaxIncluded: '0', priceTaxExcluded: '0'};

      edit(tracker, fields, 'priceTaxIncluded', '12');

      expect(fields.priceTaxIncluded).to.equal('12');
      expect(fields.priceTaxExcluded).to.equal('9.756098');
    });

    it('still converts in the other direction', () => {
      const tracker = new UpdateOriginTracker();
      const fields = {priceTaxIncluded: '0', priceTaxExcluded: '0'};

      edit(tracker, fields, 'priceTaxExcluded', '10');

      expect(fields.priceTaxExcluded).to.equal('10');
      expect(fields.priceTaxIncluded).to.equal('12.300000');
    });
  });
});
