/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createRouter, createWebHistory} from 'vue-router';
import Overview from '@app/pages/translations/components/app.vue';

export default createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'overview',
      component: async () => Overview,
    },
  ],
});
