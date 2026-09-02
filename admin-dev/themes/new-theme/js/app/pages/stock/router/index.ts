/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createRouter, createWebHistory, RouteLocationNormalized} from 'vue-router';
import Overview from '@app/pages/stock/components/overview/index.vue';
import Movements from '@app/pages/stock/components/movements/index.vue';

const router = createRouter({
  history: createWebHistory(`${window.data.baseUrl}${/(index\.php)/.exec(window.location.href) ? '/index.php' : ''}/sell/stocks`),
  routes: [
    {
      path: '/',
      name: 'overview',
      component: async () => Overview,
    },
    {
      path: '/movements',
      name: 'movements',
      component: async () => Movements,
    },
  ],
});

function hasTokenQueryParams(route: RouteLocationNormalized) {
  return '_token' in route.query;
}
router.beforeEach((to, from, next) => {
  if (!hasTokenQueryParams(to) && hasTokenQueryParams(from)) {
    next({name: to.name!, query: from.query});
  } else {
    next();
  }
});

export default router;
