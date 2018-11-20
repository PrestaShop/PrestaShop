/* eslint-disable */

import Vue from 'vue';
import Vuex from 'vuex';
import VueRouter from 'vue-router'
import Translation from 'app/pages/stock/mixins/translate';
import App from 'app/pages/stock/components/app.vue';

Vue.use(Vuex);
Vue.use(VueRouter);
Vue.mixin(Translation);

let router = new VueRouter({path: 'overview'})
const PAGE_COUNT_MOCK = 2;
const PAGINATION_INDEX_MOCK = 1;

const mockedStore = {
  state: {
    isReady: true,
    translations: {},
    movementsTypes: [],
    categories: [],
    suppliers: [],
    totalPages: PAGE_COUNT_MOCK,
    pageIndex: PAGINATION_INDEX_MOCK,
  },
  getters: {
    suppliers: () => {
      return [];
    },
    categories: () => {
      return [];
    },
  },
  actions: {
    getSuppliers() {

    },
    getCategories() {

    },
    isLoading() {

    },
    getMovements() {

    }
  }
}

window.data = {
  catalogUrl: ''
}

$.fn.datetimepicker = function() {
  return {
    on: function() {

    }
  }
};


describe('app.vue', () => {

  const Constructor = Vue.extend(App);
  var vm = new Constructor({
    store: new Vuex.Store(mockedStore),
    router
  }).$mount();

  it('should exist', () => {
    expect(vm.$el).to.exist;
  });

  it('should have stock app class', function () {
    $(vm.$el).should.have.class('stock-app');
  });

  it('should not display app when translations are not loaded', function () {
    mockedStore.state.isReady = false;
    vm = new Constructor({
      store: new Vuex.Store(mockedStore),
      router
    }).$mount();
    $(vm.$el).should.not.have.class('stock-app');
  });

  it('should have a data property called filters', function () {
    expect(App.data().filters).to.exist;
  });

  it('should have 3 children components', function () {
    expect(App.components.StockHeader).to.exist;
    expect(App.components.Search).to.exist;
    expect(App.components.PSPagination).to.exist;
  });

  it('should have a computed property isReady', function () {
    expect(vm.isReady).to.be.false;
  });

  it('should have a computed property pagesCount', function () {
    expect(vm.pagesCount).to.equal(PAGE_COUNT_MOCK);
  });

  it('should have a computed property currentPagination', function () {
    expect(vm.currentPagination).to.equal(PAGINATION_INDEX_MOCK);
  });

  it('should have a method fetch which call actions', function () {
    var spy = sinon.spy(vm.$store, 'dispatch');
    vm.fetch();
    assert(spy.calledWith('isLoading'));
    assert(spy.calledWith('getMovements'));
    spy.restore();
  });
});
