/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Static curation of the constraint whitelist into the "Add a constraint" menu groups. Names
 * missing from this table (e.g. added later to the whitelist) fall back to the "Other" group —
 * grouping is a display concern only, the catalog payload stays the source of truth for which
 * names exist.
 */
const CONSTRAINT_GROUPS: Record<string, string[]> = {
  Presence: ['NotBlank', 'NotNull', 'Blank', 'IsNull'],
  Text: [
    'Length', 'Regex', 'TypedRegex', 'Email', 'Url', 'Json', 'Uuid', 'Ulid', 'Ip', 'Cidr',
    'Hostname', 'CssColor', 'NoSuspiciousCharacters', 'CleanHtml',
  ],
  Number: ['Positive', 'PositiveOrZero', 'Negative', 'NegativeOrZero', 'Range', 'DivisibleBy', 'Luhn'],
  Comparison: [
    'EqualTo', 'NotEqualTo', 'IdenticalTo', 'NotIdenticalTo',
    'LessThan', 'LessThanOrEqual', 'GreaterThan', 'GreaterThanOrEqual',
  ],
  'Date & time': ['Date', 'DateTime', 'Time', 'Timezone'],
  Choice: ['Choice', 'Count', 'Type', 'IsTrue', 'IsFalse'],
  'Locale & format': [
    'Country', 'Language', 'Locale', 'Currency', 'Iban', 'Bic', 'Isbn', 'Issn', 'Isin', 'CardScheme',
  ],
  Multilingual: ['DefaultLanguage', 'All', 'AtLeastOneOf', 'Sequentially', 'Collection'],
};

export interface ConstraintGroup {
  group: string;
  names: string[];
}

/**
 * Splits the catalog's names into the curated display groups, keeping unknown names visible
 * under "Other".
 */
export default function groupConstraintNames(allNames: string[]): ConstraintGroup[] {
  const grouped: ConstraintGroup[] = [];
  const seen = new Set<string>();

  Object.entries(CONSTRAINT_GROUPS).forEach(([group, names]) => {
    const present = names.filter((name) => allNames.includes(name));
    present.forEach((name) => seen.add(name));

    if (present.length > 0) {
      grouped.push({group, names: present});
    }
  });

  const other = allNames.filter((name) => !seen.has(name));

  if (other.length > 0) {
    grouped.push({group: 'Other', names: other});
  }

  return grouped;
}
