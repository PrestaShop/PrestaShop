/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {JSDOM} from 'jsdom';
import {createRequire} from 'module';

const require = createRequire(import.meta.url);

const TAGGABLE_FIELD_MODULE = '../../js/components/taggable-field';

describe('TaggableField', () => {
  let $;
  let TaggableField;

  beforeEach(() => {
    const dom = new JSDOM(
      '<!doctype html><html><body><input id="tags" name="tags" type="text" value=""></body></html>',
      {pretendToBeVisual: true},
    );

    global.window = dom.window;
    global.document = dom.window.document;

    // jQuery binds to global.window at require time, and bootstrap-tokenfield pulls jQuery through
    // its own require(): both have to resolve to the same instance, so drop them from the cache and
    // load jQuery only once global.document exists.
    delete require.cache[require.resolve('jquery')];
    delete require.cache[require.resolve('bootstrap-tokenfield')];

    $ = require('jquery');
    dom.window.$ = $;
    dom.window.jQuery = $;
    global.$ = $;
    global.jQuery = $;

    require('bootstrap-tokenfield');

    delete require.cache[require.resolve(TAGGABLE_FIELD_MODULE)];
    // eslint-disable-next-line global-require
    TaggableField = require(TAGGABLE_FIELD_MODULE).default;
  });

  /**
   * maxlength caps the whole input, so pasting a comma separated list longer than the per tag limit
   * is truncated by the browser before tokenfield ever splits it. The limit is about one tag.
   */
  it('does not cap the length of the input the tags are typed into', () => {
    new TaggableField({tokenFieldSelector: '#tags', options: {maxCharacters: 32}});

    const $tokenInput = $('#tags').siblings('.token-input');

    expect($tokenInput.length).to.be.greaterThan(0);
    expect($tokenInput.attr('maxlength')).to.equal(undefined);
  });

  it('refuses a single tag longer than the per tag limit', () => {
    new TaggableField({tokenFieldSelector: '#tags', options: {maxCharacters: 32}});

    $('#tags').tokenfield('setTokens', `short,${'x'.repeat(33)},other`);

    const tokens = $('#tags').tokenfield('getTokens').map((token) => token.value);
    expect(tokens).to.deep.equal(['short', 'other']);
  });

  it('does not restrict anything when no limit is given', () => {
    new TaggableField({tokenFieldSelector: '#tags', options: {}});

    $('#tags').tokenfield('setTokens', `${'x'.repeat(80)},tail`);

    const tokens = $('#tags').tokenfield('getTokens').map((token) => token.value);
    expect(tokens).to.deep.equal(['x'.repeat(80), 'tail']);
  });
});
