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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

// Create a fake Db class on the global namespace

namespace {
    abstract class Db extends \DbCore
    {
        public static function getInstance($master = true)
        {
            return new FakeDb();
        }
    }

    class FakeDb extends Db
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
}

namespace Tests\Unit\Classes {
    use PHPUnit\Framework\TestCase;
    use RequestSql;

    class RequestSqlTest extends TestCase
    {
        /**
         * @dataProvider provider
         */
        public function testValidateSql(string $sql, bool $valid): void
        {
            $requestSql = $this->createRequestSqlMock();
            $parser = $requestSql->parsingSql($sql);
            $this->assertSame($valid, $requestSql->validateParser($parser, false, $sql));
        }

        public function provider(): iterable
        {
            yield ['select * from ps_table', true];
            yield ['select * from ps_notexistingtable', false];
            yield ['select * from ps_table WHERE EXISTS (select 1 from ps_table)', true];
            yield ['select * from ps_table WHERE EXISTS 1', false];
            yield ['wrong * from ps_table', false];
        }

        private function createRequestSqlMock()
        {
            $requestSql = $this->getMockBuilder(RequestSql::class)
                ->setMethods(['getTables'])
                ->getMock()
            ;

            $requestSql->method('getTables')->willReturn([
                'ps_table',
            ]);

            return $requestSql;
        }
    }
}
