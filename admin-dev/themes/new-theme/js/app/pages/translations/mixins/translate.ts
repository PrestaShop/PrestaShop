/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import {defineComponent} from 'vue';

export default defineComponent({
  methods: {
    trans(key: string): string {
      return this.$store.getters.translations[key];
    },
  },
});
