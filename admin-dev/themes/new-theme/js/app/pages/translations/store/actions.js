import Vue from 'vue';
import VueResource from 'vue-resource';
import * as types from './mutation-types';
import { showGrowl } from 'app/utils/growl';

Vue.use(VueResource);

export const getTranslations = ({ commit }) => {
  let url = window.data.translationUrl;
  Vue.http.get(url).then(function(response) {
    commit(types.SET_TRANSLATIONS, response.body);
  }, function(error) {
    return showGrowl('error', error.statusText);
  });
};
