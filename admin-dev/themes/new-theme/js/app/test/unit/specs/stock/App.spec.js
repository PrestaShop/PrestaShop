/* eslint-disable */

import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router'
import Translation from 'app/pages/stock/mixins/translate';
import App from 'app/pages/stock/components/app.vue';

Vue.use(Vuex);
Vue.use(VueRouter);
Vue.mixin(Translation);

const mockedStore = {
  state: {
    isReady: true,
    translations: {}
  }
}

window.data = {
  catalogUrl: ''
}


describe('app.vue', () => {
  it('should render correct contents', () => {
    const Constructor = Vue.extend(App);
    const vm = new Constructor({
      store: new Vuex.Store(mockedStore)
    }).$mount();
    expect(vm.$el).to.exist;
  })
});
