/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import ProductSubmitFieldsManager from '@pages/product/edit/manager/product-submit-fields-manager';

const makeField = (tag, disabled, attributes = []) => ({
  tag,
  disabled,
  attributes,
  toggleAttribute(name, value) {
    if (name === 'disabled') {
      this.disabled = value;
    }
  },
});

const matches = (field, selector) => selector.split(',').some((part) => {
  const [, tag, attribute] = part.trim().match(/^(\w+)\[([\w-]+)\]$/) || [];

  return tag === field.tag && field.attributes.includes(attribute);
});

const makeForm = (fields) => {
  const handlers = {};

  return {
    handlers,
    on(event, callback) {
      handlers[event] = callback;
    },
    get() {
      return {
        querySelectorAll: (selector) => fields.filter((field) => matches(field, selector)),
      };
    },
  };
};

describe('ProductSubmitFieldsManager', () => {
  it('re-enables disabled fields marked by the toggler before the form is posted', () => {
    const fields = [
      makeField('input', true, ['data-reenable-on-submit']),
      makeField('select', true, ['data-reenable-on-submit']),
      makeField('textarea', true, ['data-reenable-on-submit']),
    ];
    const form = makeForm(fields);

    new ProductSubmitFieldsManager(form);
    form.handlers.submit();

    expect(fields.map((field) => field.disabled)).to.deep.equal([false, false, false]);
  });

  it('leaves disabled fields that are not marked disabled', () => {
    const field = makeField('input', true);
    const form = makeForm([field]);

    new ProductSubmitFieldsManager(form);
    form.handlers.submit();

    expect(field.disabled).to.equal(true);
  });

  it('does nothing when the form element is missing', () => {
    const form = {
      handlers: {},
      on(event, callback) {
        this.handlers[event] = callback;
      },
      get() {
        return undefined;
      },
    };

    new ProductSubmitFieldsManager(form);

    expect(() => form.handlers.submit()).to.not.throw();
  });
});
