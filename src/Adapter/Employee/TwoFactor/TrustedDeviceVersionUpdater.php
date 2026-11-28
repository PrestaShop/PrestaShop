<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee\TwoFactor;

use InvalidArgumentException;

/**
 * Builds comparisons against the stored row, rather than a possibly stale employee.
 */
final class TrustedDeviceVersionUpdater
{
    public const COLUMN = 'two_factor_trusted_version';

    /**
     * Values are SQL literals already formatted by ObjectModel, or DBAL placeholders.
     * Only the fixed column names below are used to build SQL.
     *
     * @param array<string, string> $values
     */
    public function getIncrementExpression(array $values, bool $force = false): ?string
    {
        if ($force) {
            return '`' . self::COLUMN . '` + 1';
        }

        $conditions = [];
        foreach (['passwd', 'email'] as $column) {
            if (array_key_exists($column, $values)) {
                $conditions[] = sprintf('NOT (HEX(`%s`) <=> HEX(%s))', $column, $values[$column]);
            }
        }
        foreach (['two_factor_enabled', 'two_factor_totp_enabled', 'two_factor_email_enabled'] as $column) {
            if (array_key_exists($column, $values)) {
                $conditions[] = sprintf('NOT (COALESCE(`%s`, 0) <=> COALESCE(%s, 0))', $column, $values[$column]);
            }
        }
        if (array_key_exists('two_factor_totp_secret', $values)) {
            $effectiveTotp = $values['two_factor_totp_enabled'] ?? '`two_factor_totp_enabled`';
            $conditions[] = sprintf(
                '((COALESCE(`two_factor_totp_enabled`, 0) = 1 OR COALESCE(%s, 0) = 1)'
                . " AND NOT (HEX(COALESCE(`two_factor_totp_secret`, '')) <=> HEX(COALESCE(%s, ''))))",
                $effectiveTotp,
                $values['two_factor_totp_secret']
            );
        }

        if ([] === $conditions) {
            return null;
        }

        return '`' . self::COLUMN . '` + CASE WHEN (' . implode(' OR ', $conditions) . ') THEN 1 ELSE 0 END';
    }

    /**
     * ObjectModel has already escaped and filtered these fields. Mirror Db::update's
     * literal/null handling without escaping a second time.
     *
     * @param array<string, mixed> $fields
     *
     * @return array<string, mixed>
     */
    public function getLegacyUpdateFields(array $fields, bool $nullValues, bool $force): array
    {
        unset($fields[self::COLUMN]);
        $values = [];
        foreach (['passwd', 'email', 'two_factor_enabled', 'two_factor_totp_enabled', 'two_factor_email_enabled', 'two_factor_totp_secret'] as $column) {
            if (!array_key_exists($column, $fields)) {
                continue;
            }

            $value = $fields[$column];
            if (is_array($value)) {
                if (($value['type'] ?? null) !== 'sql' || ($value['value'] ?? null) !== 'NULL') {
                    throw new InvalidArgumentException('Unexpected SQL expression in employee security fields.');
                }
                $values[$column] = 'NULL';
            } else {
                $values[$column] = $nullValues && (null === $value || '' === $value)
                    ? 'NULL'
                    : "'" . $value . "'";
            }
        }

        $expression = $this->getIncrementExpression($values, $force);
        if (null === $expression) {
            return $fields;
        }

        // First assignment reads the old fields with left-to-right evaluation.
        // MariaDB SIMULTANEOUS_ASSIGNMENT also evaluates against the old row.
        return [self::COLUMN => ['type' => 'sql', 'value' => $expression]] + $fields;
    }
}
