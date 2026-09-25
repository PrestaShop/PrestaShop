/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const {expect} = require('chai');
const {JSDOM} = require('jsdom');

const dom = new JSDOM('<!doctype html><html><body></body></html>');
// jquery binds to the ambient document when there is one, so publish the jsdom globals first
global.window = dom.window;
global.document = dom.window.document;
const $ = require('jquery');
dom.window.$ = $;
dom.window.jQuery = $;
global.$ = $;
global.jQuery = $;

const {default: EntitySearchWidget} = require('../../js/components/entity-search-widget');
const {default: EntitySearchInput, ENTITY_SEARCH_INPUT_DATA_KEY} = require('../../js/components/entity-search-input');

// What EntitySearchInputType renders, reduced to the parts this component looks at
const widget = (id) => `<div id="${id}" class="entity-search-widget" data-remote-url="/search?q=__QUERY__"></div>`;

describe('EntitySearchWidget', () => {
  it('leaves widgets already built by the page alone, so no input gets two typeaheads', () => {
    document.body.innerHTML = widget('owned_a') + widget('owned_b');
    const owners = {owned_a: 'page instance A', owned_b: 'page instance B'};
    Object.keys(owners).forEach((id) => $(`#${id}`).data(ENTITY_SEARCH_INPUT_DATA_KEY, owners[id]));

    new EntitySearchWidget();

    Object.keys(owners).forEach((id) => {
      expect($(`#${id}`).data(ENTITY_SEARCH_INPUT_DATA_KEY), id).to.equal(owners[id]);
    });
  });

  it('only considers the containers rendered by EntitySearchInputType', () => {
    document.body.innerHTML = `${widget('a')}<div id="unrelated"></div>`;
    $('#a').data(ENTITY_SEARCH_INPUT_DATA_KEY, 'page instance');

    const searchWidget = new EntitySearchWidget();

    expect(searchWidget.getEntitySearchInput($('#a'))).to.equal('page instance');
    expect(searchWidget.getEntitySearchInput($('#unrelated'))).to.be.undefined;
  });

  it('tells the caller what to use when EntitySearchInput is built without a container', () => {
    expect(() => new EntitySearchInput()).to.throw(/EntitySearchWidget/);
  });
});
