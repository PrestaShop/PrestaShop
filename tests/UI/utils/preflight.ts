/*
 * Preflight safety check: refuse to run test campaigns against a database that
 * contains real payment-gateway data.
 *
 * Test campaigns mutate the shop under test: they create, edit and delete
 * orders, and they set order statuses through the BO. Payment modules
 * (PayPal, Stripe, ps_checkout, ...) react to status transitions with live
 * API calls — for example, setting an order to "Refunded" triggers a real
 * refund against the original transaction. A shop cloned from production can
 * therefore lose real money or send real e-mails during a test run.
 *
 * This check queries the configured database (global.INSTALL.DB_SERVER /
 * DB_NAME / DB_USER / DB_PASSWD / DB_PREFIX, same source as the DB helpers in
 * commonTests) and aborts the whole mocha run when it finds evidence of real
 * gateway data:
 *   - rows in tables belonging to known payment modules, discovered generically
 *     through information_schema rather than a fixed name list, so renamed or
 *     less common gateway schemas are still caught;
 *   - non-empty transaction references in the core order_payment table
 *     (a gateway-agnostic signal: real card/wallet payments write one).
 *
 * LIMITATION — this is a tripwire, not a proof of safety: a cloned shop can
 * hold real orders and customers with the transaction references already
 * stripped, and this check cannot see that. It only catches the common shape
 * of "database copied from a running shop". Running the suite against a
 * dedicated test shop remains mandatory. See the target-environment warning
 * in tests/UI/README.md.
 *
 * Set ALLOW_REAL_DATA=true (or =1) to run against such a database knowingly
 * (not recommended). If the configured database is unreachable, the check
 * fails closed: no test campaign is started.
 */

import type { Connection } from 'mysql2/promise';
import { createConnection } from 'mysql2/promise';

/**
 * Table-name patterns of known payment modules (without the PS prefix). Each
 * pattern matches the tables the module ships (module tables are named
 * `<prefix><module_name>...`); intentionally excludes ambiguous short names
 * that would match unrelated tables. Missing one module is caught by the
 * gateway-agnostic order_payment check below.
 */
const GATEWAY_TABLE_PATTERNS: readonly string[] = [
  'paypal%', // paypal
  'stripe%', // ps_stripe / stripe
  'ps!_checkout%', // official PrestaCheckout ('!'-escaped underscore = literal)
  'mollie%', // mollie
  'payplug%', // payplug
  'worldline%', // worldline
  'amazonpay%', // amazonpay
];

/** True for the documented bypass: ALLOW_REAL_DATA set to true or 1. */
function isRealDataOverrideEnabled(): boolean {
  const raw: string = (process.env.ALLOW_REAL_DATA ?? '').trim().toLowerCase();
  return raw === 'true' || raw === '1';
}

/** True when an error is "table does not exist" from THIS connection's
 * query: match the driver code and its errno together, so an error from a
 * different cause can never be mistaken for an absent table. */
function isNoSuchTable(error: unknown): boolean {
  const err = error as { code?: string; errno?: number };
  return err.code === 'ER_NO_SUCH_TABLE' && err.errno === 1146;
}

/** Quote a MySQL identifier (backticks escaped) for safe embedding in SQL. */
function quoteIdentifier(identifier: string): string {
  return '`' + identifier.replace(/`/g, '``') + '`';
}

/** Escape LIKE wildcards in a literal prefix for patterns using ESCAPE '!'. */
function escapeLikeLiteral(value: string): string {
  return value.replace(/[!%_]/g, (c) => '!' + c);
}

/** Count rows of one discovered table. Any query error propagates: the
 * table existed moments ago in the same connection, so its disappearance or
 * an access error must fail the run closed, not count as zero. */
async function countTableRows(connection: Connection, table: string): Promise<number> {
  const [rows] = await connection.query(`SELECT COUNT(*) AS total FROM ${quoteIdentifier(table)}`);
  return Number((rows as Array<Record<string, unknown>>)[0]?.total ?? 0);
}

/**
 * Wrap an unexpected driver error in the standard PREFLIGHT ABORT shape so
 * every failure reads as the tripwire firing, with remediation guidance.
 * Only the driver's machine-readable fields are carried over: the raw driver
 * message can embed connection details and is never included.
 */
function asPreflightAbort(error: unknown, what: string): Error {
  const driverError = error as { code?: string; errno?: number };
  return new Error(
    'PREFLIGHT ABORT: ' + what
    + `${driverError.code ? ` [${driverError.code}]` : ''}`
    + `${typeof driverError.errno === 'number' ? ` errno=${driverError.errno}` : ''}.\n`
    + 'Without a complete check the target cannot be proven free of real '
    + 'payment data, so the run is refused. Run campaigns against a '
    + 'dedicated test shop, or set ALLOW_REAL_DATA=true to skip this check.',
  );
}

/**
 * @function checkNoRealGatewayData
 * @description Throw when the configured database holds real payment-gateway
 * data. Wired into the global mocha `before` hook in utils/setup.ts: mocha
 * registers hooks from --file inputs first, so this runs once per run before
 * any campaign file's hooks or tests execute.
 */
export async function checkNoRealGatewayData(): Promise<void> {
  if (isRealDataOverrideEnabled()) {
    console.warn(
      '*** PREFLIGHT OVERRIDE ACTIVE — real-data check SKIPPED ***\n'
      + 'ALLOW_REAL_DATA is set, so the target database was NOT verified: it may '
      + 'contain real orders and live payment credentials, and campaigns may '
      + 'trigger live refunds or e-mails. This bypass also skips the database '
      + 'reachability check.',
    );
    return;
  }

  // The suite config object must exist before any field is read; otherwise a
  // missing config would surface as a raw TypeError instead of a fail-closed
  // preflight abort.
  if (!global.INSTALL) {
    throw new Error(
      'PREFLIGHT ABORT: the suite config object (global.INSTALL) is not '
      + 'loaded; cannot verify that the target database is free of real '
      + 'payment data. Run campaigns through utils/setup.ts.',
    );
  }

  const dbUser: string = global.INSTALL.DB_USER;
  const dbName: string = global.INSTALL.DB_NAME;
  const dbHost: string = global.INSTALL.DB_SERVER;
  const dbSecret: string = global.INSTALL.DB_PASSWD;
  const dbPrefix: string = global.INSTALL.DB_PREFIX ?? '';

  for (const [name, value] of [
    ['DB_SERVER', dbHost],
    ['DB_NAME', dbName],
    ['DB_USER', dbUser],
  ] as Array<[string, string]>) {
    if (!value) {
      throw new Error(
        `PREFLIGHT ABORT: ${name} is not configured; cannot verify that the `
        + 'target database is free of real payment data. (An empty DB_PASSWD is '
        + 'allowed for passwordless local databases.)',
      );
    }
  }

  // MySQL identifiers may also contain '$'; identifiers with other characters
  // cannot be embedded safely, so the run is refused rather than guessed at.
  if (!/^[A-Za-z0-9_$]*$/.test(dbPrefix)) {
    throw new Error(
      `PREFLIGHT ABORT: unexpected DB_PREFIX '${dbPrefix}' — `
      + 'expected only letters, digits, underscore or dollar sign '
      + '(check _DB_PREFIX_ in the shop config parameters file).',
    );
  }

  const connectionOptions = {
    host: dbHost,
    database: dbName,
    user: dbUser,
    // The credential value comes from the environment (global.INSTALL.DB_PASSWD)
    // exactly like the DB helpers in commonTests do; it is handed to the driver
    // only, never logged or included in error messages.
    password: dbSecret,
    connectTimeout: 10000,
  };

  let connection: Connection;
  try {
    connection = await createConnection(connectionOptions);
  } catch (error: unknown) {
    // Fail closed with an explicit message so this reads as the tripwire
    // firing rather than as a generic test-infrastructure failure. Only the
    // driver's machine-readable fields are reported: the raw driver message
    // can embed connection details, so it is never included.
    const driverError = error as { code?: string; errno?: number };
    // Host and schema name are echoed only in bounded, truncated form: they
    // come from operator configuration, but unbounded values in a CI log are
    // still noise, so each is capped.
    throw new Error(
      'PREFLIGHT ABORT: cannot reach the configured database '
      + `(${dbHost.slice(0, 120)}/${dbName.slice(0, 64)})`
      + `${driverError.code ? ` [${driverError.code}]` : ''}`
      + `${typeof driverError.errno === 'number' ? ` errno=${driverError.errno}` : ''}\n`
      + 'Without database access the preflight check cannot prove the target '
      + 'is free of real payment data, so the run is refused. Set '
      + 'ALLOW_REAL_DATA=true to skip this check.',
    );
  }

  try {
    const findings: string[] = [];

    // 0. Prefix sanity: every PrestaShop schema has the core configuration
    // table under its prefix. If it is absent, the configured prefix (or the
    // schema) is wrong and every count below would pass vacuously — refuse
    // rather than report a clean result for the wrong namespace.
    let confTotal: number;
    try {
      confTotal = await countTableRows(connection, `${dbPrefix}configuration`);
    } catch (error: unknown) {
      if (isNoSuchTable(error)) {
        confTotal = 0;
      } else {
        throw asPreflightAbort(error, `cannot verify ${dbPrefix}configuration`);
      }
    }
    if (confTotal === 0) {
      throw new Error(
        `PREFLIGHT ABORT: no ${dbPrefix}configuration table in ${dbName} — `
        + 'the configured DB_PREFIX or DB_NAME does not address a PrestaShop '
        + 'database, so the checks below would prove nothing. Correct '
        + 'DB_PREFIX/DB_NAME (see the shop config parameters file).',
      );
    }

    // 1. Discover gateway module tables from the schema itself, instead of a
    // fixed table list, so module schema variants are still caught. The schema
    // is the configured DB_NAME bound as a parameter (not DATABASE(), which
    // could differ from the configured name if the connection defaulted).
    const likePatterns: string[] = [
      dbName,
      ...GATEWAY_TABLE_PATTERNS.map(
        (pattern) => escapeLikeLiteral(dbPrefix) + pattern,
      ),
    ];
    let gwRows: unknown;
    try {
      [gwRows] = await connection.query(
        'SELECT table_name AS name FROM information_schema.tables '
        + 'WHERE table_schema = ? AND ('
        + GATEWAY_TABLE_PATTERNS.map(() => "table_name LIKE ? ESCAPE '!'").join(' OR ')
        + ')',
        likePatterns,
      );
    } catch (error: unknown) {
      throw asPreflightAbort(error, 'cannot query information_schema.tables');
    }

    for (const row of gwRows as Array<{ name: string }>) {
      let total: number;
      try {
        total = await countTableRows(connection, row.name);
      } catch (error: unknown) {
        throw asPreflightAbort(error, `cannot count rows of ${row.name}`);
      }
      if (total > 0) {
        findings.push(`table ${row.name}: ${total} row(s)`);
      }
    }

    // 2. Gateway-agnostic signal: real payments write a transaction
    // reference into the core order_payment table (column transaction_id;
    // the module name lives in payment_method — the table has no `gateway`
    // column). A shop without that table (fresh install) has no payments at
    // all, so it counts as clean: the ONE place where a no-such-table error
    // maps to clean.
    try {
      const [paymentRows] = await connection.query(
        'SELECT payment_method, COUNT(*) AS total FROM ' + quoteIdentifier(`${dbPrefix}order_payment`)
        + " WHERE transaction_id IS NOT NULL AND TRIM(transaction_id) <> ''"
        + ' GROUP BY payment_method',
      );
      for (const row of paymentRows as Array<{ payment_method: string; total: number }>) {
        if (Number(row.total) > 0) {
          findings.push(
            `table ${dbPrefix}order_payment: ${row.total} row(s) with a non-empty `
            + `transaction_id (payment method: ${row.payment_method || 'unknown'})`,
          );
        }
      }
    } catch (error: unknown) {
      if (!isNoSuchTable(error)) {
        throw asPreflightAbort(error, `cannot inspect ${dbPrefix}order_payment`);
      }
    }

    if (findings.length > 0) {
      throw new Error(
        'PREFLIGHT ABORT: the target database appears to contain real payment data:\n'
        + findings.map((f) => `  - ${f}`).join('\n')
        + '\nTest campaigns mutate orders and their statuses, which can trigger'
        + ' live refunds, capture calls and e-mails to real customers.'
        + '\nRun against a dedicated test shop (a fresh install or a sanitized copy'
        + ' with gateway modules uninstalled and transaction references removed).'
        + '\nSet ALLOW_REAL_DATA=true only if you accept these risks.',
      );
    }
  } finally {
    try {
      await connection.end();
    } catch {
      // Closing must never mask the abort error raised above.
    }
  }
}
