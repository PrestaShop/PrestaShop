<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Fake Db class for unit tests to avoid database dependency.
 * This mock allows tests to run without requiring a real database connection.
 */
// Create a fake Db class on the global namespace

namespace {
    abstract class Db extends DbCore
    {
        public static function getInstance($master = true)
        {
            return new MockDb();
        }
    }

    class MockDb extends Db
    {
        public function __construct()
        {
        }

        /* @phpstan-ignore-next-line */
        public function connect()
        {
        }

        /* @phpstan-ignore-next-line */
        public function disconnect()
        {
        }

        protected function _query($sql)
        {
            return true;
        }

        /* @phpstan-ignore-next-line */
        protected function _numRows($result)
        {
        }

        /* @phpstan-ignore-next-line */
        public function Insert_ID()
        {
        }

        /* @phpstan-ignore-next-line */
        public function Affected_Rows()
        {
        }

        /* @phpstan-ignore-next-line */
        public function nextRow($result = false)
        {
        }

        /* @phpstan-ignore-next-line */
        protected function getAll($result = false)
        {
            /* @phpstan-ignore-next-line */
            return true;
        }

        /* @phpstan-ignore-next-line */
        public function getVersion()
        {
        }

        /* @phpstan-ignore-next-line */
        public function _escape($str)
        {
            return $str;
        }

        /* @phpstan-ignore-next-line */
        public function getMsgError()
        {
        }

        /* @phpstan-ignore-next-line */
        public function getNumberError()
        {
        }

        /* @phpstan-ignore-next-line */
        public function set_db($db_name)
        {
        }

        /* @phpstan-ignore-next-line */
        public function getBestEngine()
        {
        }
    }

    // Initialize the Db instance for all tests
    if (!isset(Db::$instance[0])) {
        Db::$instance[0] = new MockDb();
    }
}
