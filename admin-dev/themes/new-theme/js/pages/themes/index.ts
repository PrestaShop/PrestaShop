/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ResetThemeLayoutsHandler from './reset-theme-layouts-handler';
import UseThemeHandler from './use-theme-handler';
import MultiStoreRestrictionField from '../../components/multi-store-restriction-field/multi-store-restriction-field';
import DeleteThemeHandler from './delete-theme-handler';

const {$} = window;

$(() => {
  new ResetThemeLayoutsHandler();
  new MultiStoreRestrictionField();
  new UseThemeHandler();
  new DeleteThemeHandler();
});
