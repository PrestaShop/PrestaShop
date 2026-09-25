/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
const {expect} = require('chai');
const {JSDOM} = require('jsdom');

const TREE = `
  <div class="material-choice-tree-container js-choice-tree-container" id="form_id_parent">
    <ul class="choice-tree">
      <li class="expanded">
        <span class="js-input-wrapper"><input type="checkbox" value="1"></span>
        <ul><li><span class="js-input-wrapper"><input type="checkbox" value="2"></span></li></ul>
      </li>
    </ul>
  </div>`;

/**
 * The components read `const {$} = window` when the module is evaluated, so the DOM and jQuery have
 * to exist as globals before they are required. That is why this file requires them lazily instead
 * of importing them at the top.
 */
const load = (body) => {
  const dom = new JSDOM(`<!doctype html><html><body>${body}</body></html>`, {url: 'http://localhost/'});
  global.window = dom.window;
  global.document = dom.window.document;

  // Fresh module instances per test, so one test's captured `$` never leaks into the next. jQuery is
  // reloaded too: its UMD wrapper exports a factory only while no global document exists, and returns
  // a jQuery already bound to the global once one does, so the shape depends on load order.
  Object.keys(require.cache)
    .filter((key) => key.includes('choice-tree') || key.includes(`${'jquery'}`))
    .forEach((key) => delete require.cache[key]);

  /* eslint-disable global-require */
  const jqExport = require('jquery');
  const jq = typeof jqExport === 'function' && !jqExport.fn ? jqExport(dom.window) : jqExport;
  /* eslint-enable global-require */
  global.$ = jq;
  global.jQuery = jq;
  dom.window.$ = jq;
  dom.window.jQuery = jq;

  /* eslint-disable global-require */
  const ChoiceTree = require('../../js/components/form/choice-tree').default;
  const ChoiceTreeWidget = require('../../js/components/form/choice-tree-widget').default;
  /* eslint-enable global-require */

  return {dom, $: jq, ChoiceTree, ChoiceTreeWidget};
};

const clickFirstWrapper = (dom) => dom.window.document.querySelector('.js-input-wrapper').click();
const firstItemClass = (dom) => dom.window.document.querySelector('li').className;

describe('ChoiceTreeWidget', () => {
  /**
   * initComponents() constructs every component with no argument. ChoiceTree then received no
   * container, bound its handlers to an empty selection and did nothing at all - without throwing,
   * which is why the report describes a component that "is loaded" but "doesn't respond". It now
   * says so, and names the component that can be initialized that way.
   */
  it('refuses to be built with no container, and names the alternative', () => {
    const {ChoiceTree} = load(TREE);

    expect(() => new ChoiceTree()).to.throw('ChoiceTreeWidget');
  });

  /**
   * A page that builds a tree for a form that did not render one must keep working: the guard is
   * about a missing argument, not about a selector that matched nothing.
   */
  it('still accepts a selection that matched nothing', () => {
    const {ChoiceTree} = load(TREE);

    expect(() => new ChoiceTree('#no-such-tree')).to.not.throw();
  });

  it('makes the tree respond when initialized with no argument, as initComponents does', () => {
    const {dom, ChoiceTreeWidget} = load(TREE);

    new ChoiceTreeWidget();
    clickFirstWrapper(dom);

    expect(firstItemClass(dom)).to.equal('collapsed');
  });

  it('initializes every tree on the page', () => {
    const {dom, $, ChoiceTreeWidget} = load(TREE + TREE.replace('form_id_parent', 'form_id_other'));

    new ChoiceTreeWidget();

    expect($('.js-choice-tree-container').filter((i, el) => !!$(el).data('choiceTree')).length).to.equal(2);
  });

  /**
   * A page that builds its own tree keeps ownership of it: building a second one over the same
   * container would bind the toggle twice, so a click would expand and immediately collapse again.
   */
  it('leaves a tree the page already built alone', () => {
    const {dom, $, ChoiceTree, ChoiceTreeWidget} = load(TREE);

    const owned = new ChoiceTree('#form_id_parent');
    new ChoiceTreeWidget();

    expect($('#form_id_parent').data('choiceTree')).to.equal(owned);

    clickFirstWrapper(dom);
    expect(firstItemClass(dom)).to.equal('collapsed');
  });

  it('exposes the instance bound to a container', () => {
    const {$, ChoiceTreeWidget} = load(TREE);

    const widget = new ChoiceTreeWidget();

    expect(widget.getChoiceTree($('#form_id_parent'))).to.not.equal(undefined);
    expect(widget.getChoiceTree($('#form_id_missing'))).to.equal(undefined);
  });
});
