/* eslint-disable */
import Vue from 'vue';

Vue.config.productionTip = false;

// require all test files (files that ends with .spec.js)
var testsContext = require.context('./specs', true, /\.spec$/);
testsContext.keys().forEach(testsContext);

const srcContext = require.context('../../pages/', true, /^\.\/(?!main(\.js)?$)/);
srcContext.keys().forEach(srcContext);
