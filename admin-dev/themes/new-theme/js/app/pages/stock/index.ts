/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {createApp} from 'vue';
import App from './components/app.vue';
import store from './store';
import router from './router';

const vueApp = createApp(App).use(store).use(router);

vueApp.mount('#stock-app');
