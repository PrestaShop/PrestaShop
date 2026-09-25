/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {saveTranslations} from '../../../js/app/pages/translations/store/actions';

/**
 * The endpoint answers with one flag per translation, so a save can fail on its own while the
 * request succeeds. Reporting "successfully updated" in that case tells the merchant their work is
 * stored when it is not.
 */
describe('translations store - saveTranslations', () => {
  let growls;
  let commits;
  let dispatched;

  const givenTheServerAnswers = (ok, body) => {
    global.fetch = async () => ({
      ok,
      statusText: 'Internal Server Error',
      json: async () => body,
    });
  };

  const save = async (translationKeys) => {
    const payload = {
      url: '/edit',
      translations: translationKeys.map((key) => ({default: key})),
      store: {
        dispatch: (name, args) => dispatched.push({name, args}),
      },
    };

    await saveTranslations({commit: (type) => commits.push(type)}, payload);
  };

  beforeEach(() => {
    growls = [];
    commits = [];
    dispatched = [];

    // showGrowl() reaches for window.$.growl; the error variants are called as growl[type]().
    const growl = (options) => growls.push({type: 'success', message: options.message});
    growl.error = (options) => growls.push({type: 'error', message: options.message});
    global.window = {$: {growl}};
  });

  afterEach(() => {
    delete global.fetch;
    delete global.window;
  });

  it('reports success and clears the modified ones when every translation is stored', async () => {
    givenTheServerAnswers(true, {'key.one': true, 'key.two': true});

    await save(['key.one', 'key.two']);

    expect(growls.map((growl) => growl.type)).to.deep.equal(['success']);
    expect(commits).to.have.lengthOf(1);
    expect(dispatched[0].args.successfullySaved).to.equal(2);
  });

  it('reports how many failed and keeps them modified when the server refuses some', async () => {
    givenTheServerAnswers(true, {'key.one': true, 'key.two': false});

    await save(['key.one', 'key.two']);

    expect(growls[0].type).to.equal('error');
    expect(growls[0].message).to.contain('1 of 2');
    // Not cleared: the merchant still has the failed one in front of them to try again.
    expect(commits).to.have.lengthOf(0);
    expect(dispatched[0].args.successfullySaved).to.equal(1);
  });

  it('reports an error status instead of treating it as a save', async () => {
    // fetch() resolves on 4xx and 5xx, so this used to fall through to the success path.
    givenTheServerAnswers(false, {});

    await save(['key.one']);

    expect(growls.map((growl) => growl.type)).to.deep.equal(['error']);
    expect(commits).to.have.lengthOf(0);
    expect(dispatched).to.have.lengthOf(0);
  });
});
