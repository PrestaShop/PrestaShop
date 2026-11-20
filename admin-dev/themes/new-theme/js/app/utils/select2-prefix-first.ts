/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * select2 filters the options of a dropdown with a plain substring test and then leaves them in the
 * order they were declared. Typing "s" in the order status selector therefore offers "Processing",
 * "Payment accepted" and "Shipped" in that order, and the one the merchant meant is rarely first.
 *
 * This ranks the options that BEGIN with what was typed above the ones that merely contain it, which
 * is what makes the first result the likely one, and select2 already highlights the first result and
 * selects it on Enter.
 */
export interface Select2Result {
  text?: string;
  children?: Select2Result[];
  [key: string]: unknown;
}

function startsWithTerm(result: Select2Result, upperTerm: string): boolean {
  return typeof result.text === 'string' && result.text.toUpperCase().indexOf(upperTerm) === 0;
}

/**
 * Groups keep their own position: a group is a merchant-defined section, and promoting one because its
 * label happens to begin with the letter would move every option under it. Only the options inside a
 * group are ranked.
 */
export const sortPrefixMatchesFirst = (
  term: string,
  results: Select2Result[],
): Select2Result[] => {
  if (!Array.isArray(results)) {
    return results;
  }

  const upperTerm = (term ?? '').trim().toUpperCase();

  if (upperTerm === '') {
    return results;
  }

  const ranked = results.map((result) => (Array.isArray(result.children)
    ? {...result, children: sortPrefixMatchesFirst(term, result.children)}
    : result));

  if (ranked.some((result) => Array.isArray(result.children))) {
    return ranked;
  }

  // Array.prototype.sort is stable, so options of equal rank keep the order the shop declared them in.
  return ranked.sort(
    (a, b) => Number(startsWithTerm(b, upperTerm)) - Number(startsWithTerm(a, upperTerm)),
  );
};
