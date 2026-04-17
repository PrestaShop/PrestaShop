/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import ShowcaseCard from '@components/showcase-card/showcase-card';

interface ShowcaseExtension {
  extend: (grid: ShowcaseCard) => void;
}

export {ShowcaseCard, ShowcaseExtension};
