<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use Doctrine\DBAL\Connection;

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
     * than "a row exists" are special-cased in probe() — see tax_rules_group.
     */
    public function exists(string $table, int $id): bool
    {
        if ($id <= 0) {
            return false;
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

        // soft-deleted (historized) tax rules groups are treated as absent:
        // assigning one would resurrect it on the product
        if ('tax_rules_group' === $table) {
            $qb->andWhere('deleted = 0');
        }

        return false !== $qb->executeQuery()->fetchOne();
    }
}
