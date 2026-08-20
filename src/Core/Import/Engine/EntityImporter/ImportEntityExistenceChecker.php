<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;

/**
 * Generic entity existence probe shared by every importer and phase: one
 * memoized table+id check instead of one near-identical method per entity
 * (import only ever needs "does this id exist", never the loaded entity or
 * the domain exception).
 *
 * Only POSITIVE results are memoized (for the service lifetime — one batch
 * request — where the same ids repeat across many rows): the import itself
 * creates entities mid-run, so a miss can become a hit between two probes,
 * while an existing row never disappears during a run.
 */
class ImportEntityExistenceChecker
{
    /**
     * Tables using soft deletion: a historized row still exists but must be
     * treated as absent, otherwise assigning it would resurrect it on the
     * imported entity. Only the tables the importers actually probe are listed
     * (other soft-deleted entities - carrier, currency... - are not reachable
     * from any importer yet; add them along with their importer).
     */
    protected const SOFT_DELETE_TABLES = [
        'shop',
        'tax_rules_group',
    ];

    /**
     * @var array<string, true> memoized POSITIVE probes, keyed '<table>:<id>'
     */
    protected array $cache = [];

    public function __construct(
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
    ) {
    }

    /**
     * Whether a row of the given table (un-prefixed name) exists for the id.
     * The primary key is derived from the table name (id_<table>, the
     * PrestaShop convention). Tables whose existence semantics are richer
     * than "a row exists" are special-cased in probe() — see SOFT_DELETE_TABLES.
     *
     * @throws ImportEngineException when the table name is not a plain identifier
     */
    public function exists(string $table, int $id): bool
    {
        if ($id <= 0) {
            return false;
        }

        // the table name and its derived primary key are interpolated into the
        // query, so this generic entry point validates its own shape rather
        // than trusting every present and future importer to pass a literal
        if (1 !== preg_match('/^[a-z][a-z0-9_]*$/', $table)) {
            throw new ImportEngineException(sprintf('Invalid table name "%s" passed to the import existence checker', $table));
        }

        $cacheKey = $table . ':' . $id;
        if (isset($this->cache[$cacheKey])) {
            return true;
        }

        if (!$this->probe($table, $id)) {
            return false;
        }

        return $this->cache[$cacheKey] = true;
    }

    protected function probe(string $table, int $id): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . $table)
            ->where('id_' . $table . ' = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
        ;

        if (in_array($table, static::SOFT_DELETE_TABLES, true)) {
            $qb->andWhere('deleted = 0');
        }

        return false !== $qb->executeQuery()->fetchOne();
    }
}
