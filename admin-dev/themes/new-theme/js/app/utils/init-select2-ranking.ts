/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import {sortPrefixMatchesFirst, Select2Result} from '@app/utils/select2-prefix-first';

interface Select2Namespace {
  defaults?: {
    defaults?: Record<string, unknown>;
    set: (key: string, value: unknown) => void;
  };
}

const {$} = window;

/**
 * Wires prefix-first ranking into every select2 of the back office.
 *
 * WHY it wraps the matcher instead of replacing it: select2 hands the search term to the matcher but
 * not to the sorter, so the term has to be captured on the way past. The original matcher keeps doing
 * the matching, including its diacritics handling, so which options appear is unchanged - only the
 * order they appear in.
 *
 * WHY it has to run before DOM ready: the UI kit builds its select2 instances inside its own ready
 * handler, and an instance resolves its options from the defaults when it is constructed.
 */
export default function initSelect2PrefixFirstRanking(): void {
  const {select2} = $.fn as unknown as {select2?: Select2Namespace};
  const defaults = select2?.defaults;
  const originalMatcher = defaults?.defaults?.matcher;

  if (!defaults || typeof originalMatcher !== 'function') {
    return;
  }

  let lastTerm = '';

  defaults.set('matcher', (params: {term?: string}, data: Select2Result) => {
    lastTerm = params?.term ?? '';

    return originalMatcher(params, data);
  });
  defaults.set('sorter', (data: Select2Result[]) => sortPrefixMatchesFirst(lastTerm, data));
}
