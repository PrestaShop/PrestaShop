/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {expect} from 'chai';
import {saveTranslations} from '../../../js/app/pages/translations/store/actions';
import mutations from '../../../js/app/pages/translations/store/mutations';
import * as types from '../../../js/app/pages/translations/store/mutation-types';

describe('translations store - saveTranslations', () => {
  let growls;
  let commits;
  let dispatched;

  const givenTheServerAnswers = (ok, body) => {
    global.fetch = async () => ({
      ok,
      statusText: '',
      json: async () => body,
    });
  };

  const save = async (translationKeys) => {
    const payload = {
      url: '/edit',
      translations: translationKeys.map((key) => ({default: key})),
      store: {
        dispatch: (name, args) => dispatched.push({name, args}),
        state: {translations: {notification_partially_saved: 'Some translations could not be saved (%failed%/%total%).'}},
      },
    };

    await saveTranslations({commit: (type, args) => commits.push({type, args})}, payload);
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
    expect(growls[0].message).to.equal('Some translations could not be saved (1/2).');
    expect(commits).to.deep.equal([{type: types.REMOVE_SAVED_MODIFIED_TRANSLATIONS, args: ['key.one']}]);
    expect(dispatched[0].args.successfullySaved).to.equal(1);
  });

  it('keeps only the failed translations marked as modified', () => {
    const state = {modifiedTranslations: []};
    state.modifiedTranslations['input-1'] = {default: 'key.one'};
    state.modifiedTranslations['input-2'] = {default: 'key.two'};

    mutations[types.REMOVE_SAVED_MODIFIED_TRANSLATIONS](state, ['key.one']);

    expect(Object.values(state.modifiedTranslations)).to.deep.equal([{default: 'key.two'}]);
  });

  it('reports an error status instead of treating it as a save', async () => {
    givenTheServerAnswers(false, {error: 'Bad request'});

    await save(['key.one']);

    expect(growls).to.deep.equal([{type: 'error', message: 'Bad request'}]);
    expect(commits).to.have.lengthOf(0);
    expect(dispatched).to.have.lengthOf(0);
  });
});
