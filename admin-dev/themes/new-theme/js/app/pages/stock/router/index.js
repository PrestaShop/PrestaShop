import Vue from 'vue';
import VueRouter from 'vue-router';
import Overview from 'app/pages/stock/components/overview/index';
import Movements from 'app/pages/stock/components/movements/index';

Vue.use(VueRouter);

export default new VueRouter({
  mode: 'history',
  base: `${window.data.baseUrl}/stock`,
  routes: [
    { path: '/', name: 'overview', component: Overview },
    { path: '/movements', name: 'movements', component: Movements }
  ]
});