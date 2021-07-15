<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
abstract class Db extends DbCore
{
    /**
     * Add SQL_NO_CACHE in SELECT queries
     *
     * @var bool
     */
    public $disableCache = true;

    /**
     * Total of queries
     *
     * @var int
     */
    public $count = 0;

    /**
     * List of queries
     *
     * @var array
     */
    public $queries = [];

    /**
     * List of uniq queries (replace numbers by XX)
     *
     * @var array
     */
    public $uniqQueries = [];

    /**
     * List of tables
     *
     * @var array
     */
    public $tables = [];

    /**
     * Execute the query and log some informations
     *
     * @see DbCore::query()
     */
    public function query($sql)
    {
        $explain = false;
        if (preg_match('/^\s*explain\s+/i', $sql)) {
            $explain = true;
        }

        if (!$explain) {
            $uniqSql = preg_replace('/[\'"][a-f0-9]{32}[\'"]/', '<span style="color:blue">XX</span>', $sql);
            $uniqSql = preg_replace('/[0-9]+/', '<span style="color:blue">XX</span>', $uniqSql);
            if (!isset($this->uniqQueries[$uniqSql])) {
                $this->uniqQueries[$uniqSql] = 0;
            }
            ++$this->uniqQueries[$uniqSql];

            // No cache for query
            if ($this->disableCache && !stripos($sql, 'SQL_NO_CACHE')) {
                $sql = preg_replace('/^\s*select\s+/i', 'SELECT SQL_NO_CACHE ', trim($sql));
            }

            // Get tables in query
            preg_match_all('/(from|join)\s+`?' . _DB_PREFIX_ . '([a-z0-9_-]+)/ui', $sql, $matches);
            foreach ($matches[2] as $table) {
                if (!isset($this->tables[$table])) {
                    $this->tables[$table] = 0;
                }
                ++$this->tables[$table];
            }

            $start = microtime(true);
        }

        // Execute query
        $result = parent::query($sql);

        if (!$explain) {
            $end = microtime(true);

            $stack = debug_backtrace(0);
            while (preg_match('@[/\\\\]classes[/\\\\]db[/\\\\]@i', $stack[0]['file'])) {
                array_shift($stack);
            }
            $stack_light = [];
            foreach ($stack as $call) {
                $stack_light[] = ['file' => isset($call['file']) ? $call['file'] : 'undefined', 'line' => isset($call['line']) ? $call['line'] : 'undefined'];
            }

            $this->queries[] = [
                'query' => $sql,
                'time' => $end - $start,
                'stack' => $stack_light,
            ];
        }

        return $result;
    }
}
