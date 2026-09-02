/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Grid from '@components/grid/grid';

interface GridExtension {
  extend: (grid: Grid) => void;
}

export {Grid, GridExtension};
