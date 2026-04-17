/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
// Makes TS happy because it doesn't know about Vue SFC (not like webpack and the loader)
// so it will treat *.vue files as defineComponent
declare module '*.vue' {
  import {defineComponent} from 'vue';

  export default defineComponent;
}
