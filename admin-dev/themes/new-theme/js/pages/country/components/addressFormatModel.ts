/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Pure helpers for the country address-format builder.
 *
 * The component round-trips a multi-line, space-separated token format
 * (`Object:field` or bare `field`). Bare tokens always resolve to the
 * Address object — that's what the legacy parser does (see
 * `AddressFormat::_checkLiableAssociation` in classes/AddressFormat.php,
 * which validates bare tokens exclusively against the Address class).
 *
 * Address has firstname, lastname, company, vat_number as public properties
 * via reflection on the ObjectModel, so a bare `firstname` token in a stored
 * format is equivalent to `Address:firstname`. To place a Customer's first
 * name instead, the user must pick from the Customer tab, which emits the
 * explicit `Customer:firstname`.
 */

export type ObjectKey = string;

export interface Token {
  object: ObjectKey;
  field: string;
  raw: string;
}

export type Line = Token[];

export interface AvailableObjects {
  [object: string]: string[];
}

export interface SampleData {
  [object: string]: { [field: string]: string };
}

/**
 * Resolve a raw token string to its (object, field) pair.
 *   - explicit `Object:field` → as authored
 *   - bare `field` → Address (matches the legacy parser; invalid bare tokens
 *     still render as Address-scoped chips and are caught by the validator)
 */
export function resolveToken(raw: string): Token {
  const trimmed = raw.trim();

  if (trimmed.includes(':')) {
    const [object, field] = trimmed.split(':');

    return {object, field, raw: trimmed};
  }

  return {object: 'Address', field: trimmed, raw: trimmed};
}

/**
 * Choose the wire form for a (object, field) pair. Bare = Address, so picking
 * an Address-tab field emits its bare form; everything else gets the explicit
 * `Object:field` prefix to disambiguate (e.g. `Customer:firstname` is needed
 * because a bare `firstname` would mean Address:firstname).
 */
export function preferredRaw(object: ObjectKey, field: string): string {
  if (object === 'Address') {
    return field;
  }
  return `${object}:${field}`;
}

/**
 * Parse a raw multi-line format into structured lines.
 * Empty lines are preserved as empty arrays (so the visual editor can keep
 * a deliberately blank row the user added).
 */
export function parseFormat(text: string): Line[] {
  if (text === '') {
    return [];
  }
  return text.split('\n').map((line) => {
    const tokens = line.split(/\s+/).filter(Boolean);

    return tokens.map((t) => resolveToken(t));
  });
}

/**
 * Serialize lines back to the wire format. Preserves the user-authored raw
 * for tokens that came from parseFormat — only newly-added tokens use
 * `preferredRaw`.
 */
export function serializeLines(lines: Line[]): string {
  return lines.map((line) => line.map((t) => t.raw).join(' ')).join('\n');
}

/**
 * Render lines into preview strings using sample data. Lines whose tokens
 * all resolve to empty values are skipped.
 */
export function renderPreview(lines: Line[], sample: SampleData): string[] {
  return lines
    .map((line) => line
      .map((t) => sample[t.object]?.[t.field] ?? '')
      .filter((v) => v !== '')
      .join(' '))
    .filter((joined) => joined !== '');
}

/**
 * Required field names that are not yet placed anywhere in `lines`.
 *
 * A bare required entry (e.g. `firstname`) is satisfied only by an Address-scoped
 * placement (chip with object='Address') — placing `Customer:firstname` does NOT
 * satisfy bare `firstname`, matching the legacy validator. Prefixed entries like
 * `Country:name` require an exact `Country:name` chip.
 */
export function missingRequired(lines: Line[], requiredFields: string[]): string[] {
  const placedRaw = new Set(lines.flat().map((t) => `${t.object}:${t.field}`));

  return requiredFields.filter((req) => {
    const resolved = resolveToken(req);

    return !placedRaw.has(`${resolved.object}:${resolved.field}`);
  });
}

/**
 * Set of "Object:field" keys already present, for disabling picker pills.
 */
export function placedFieldKeys(lines: Line[]): Set<string> {
  return new Set(lines.flat().map((t) => `${t.object}:${t.field}`));
}
