/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Client-side lexer of a constraint's VERBATIM argument tail (the text between a DSL token's
 * "(...)" delimiters, carried by a constraint row's hidden options input). The typed option
 * editor renders inputs over the lexed fragments and serializes them back; quoting mirrors the
 * server-side ExtraPropertyConstraintMapper value rules so a value round-trips identically.
 * The server stays the parsing/validation authority — rows are validated on submit.
 * See tests/pages/extra-property-definition/constraint-dsl.spec.js.
 */

export interface TailToken {
  token: string;
  line: number;
}

export interface TailOption {
  /** Option name, or null for a positional (default option) value. */
  key: string | null;
  /** Verbatim DSL value fragment. */
  value: string;
}

/**
 * Splits on top-level separators only — separators inside "(...)"/"[...]" or quoted runs are
 * part of a value. A backslash escapes the next character inside a quoted run.
 */
export function tokenize(raw: string, separators = ',\n'): TailToken[] {
  const parts: TailToken[] = [];
  let buffer = '';
  let bufferLine = 1;
  let bufferStarted = false;
  let line = 1;
  let depth = 0;
  let quote: string | null = null;

  const bufferChar = (char: string): void => {
    if (!bufferStarted && char.trim() !== '') {
      bufferStarted = true;
      bufferLine = line;
    }

    buffer += char;
  };
  const flush = (): void => {
    if (buffer.trim() !== '') {
      parts.push({token: buffer.trim(), line: bufferLine});
    }

    buffer = '';
    bufferStarted = false;
  };

  for (let i = 0; i < raw.length; i += 1) {
    const char = raw[i];

    if (char === '\n') {
      line += 1;
    }

    if (quote !== null) {
      buffer += char;

      if (char === '\\' && i + 1 < raw.length) {
        i += 1;
        buffer += raw[i];
      } else if (char === quote) {
        quote = null;
      }
    } else if (char === "'" || char === '"') {
      quote = char;
      bufferChar(char);
    } else if (char === '(' || char === '[') {
      depth += 1;
      bufferChar(char);
    } else if (char === ')' || char === ']') {
      depth = Math.max(0, depth - 1);
      bufferChar(char);
    } else if (depth === 0 && separators.includes(char)) {
      flush();
    } else {
      bufferChar(char);
    }
  }

  flush();

  return parts;
}

/**
 * Lexes a verbatim argument tail into its option fragments — null when the tail does not fit
 * the named/positional shapes (the builder then falls back to a raw tail input).
 */
export function parseTail(tail: string): TailOption[] | null {
  if (tail.trim() === '') {
    return [];
  }

  const parts = tokenize(tail, ',').map(({token}) => token);
  const named = parts.length > 0 && /^\w+\s*:/.test(parts[0]);

  if (!named) {
    return parts.length === 1 ? [{key: null, value: parts[0]}] : null;
  }

  const options: TailOption[] = [];

  for (let i = 0; i < parts.length; i += 1) {
    const match = parts[i].match(/^(\w+)\s*:\s*([\s\S]+)$/);

    if (!match) {
      return null;
    }

    options.push({key: match[1], value: match[2].trim()});
  }

  return options;
}

export function serializeTail(options: TailOption[]): string {
  return options
    .filter((option) => option.value.trim() !== '')
    .map((option) => (option.key === null ? option.value : `${option.key}: ${option.value}`))
    .join(', ');
}

/**
 * Strips the quotes of a quoted DSL string value (with \\ \' \" escapes honored) for display in
 * a typed input; unquoted fragments are returned as-is.
 */
export function unquoteValue(value: string): string {
  const quoted = value.length >= 2
    && ((value.startsWith("'") && value.endsWith("'")) || (value.startsWith('"') && value.endsWith('"')));

  if (!quoted) {
    return value;
  }

  const inner = value.slice(1, -1);
  let result = '';

  for (let i = 0; i < inner.length; i += 1) {
    if (inner[i] === '\\' && i + 1 < inner.length && '\\\'"'.includes(inner[i + 1])) {
      i += 1;
    }

    result += inner[i];
  }

  return result;
}

/**
 * Quotes a string value for the DSL (inverse of unquoteValue).
 */
export function quoteValue(value: string): string {
  return `'${value.replace(/\\/g, '\\\\').replace(/'/g, "\\'")}'`;
}
