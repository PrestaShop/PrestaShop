import Vue from 'vue';
import VueRouter from 'vue-router';
import Overview from 'app/pages/translations/components/app';

Vue.use(VueRouter);

export default new VueRouter({
  mode: 'history',
  base: `${window.data.baseUrl}/translations`,
  routes: [
    {
      path: '/',
      name: 'overview',
      component: Overview
    },
  ]
});
