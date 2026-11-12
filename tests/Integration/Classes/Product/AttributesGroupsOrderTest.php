<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Combination;
use Db;
use Product;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * getAttributesGroups() orders by group position, attribute position and group name. None of those
 * identifies a row - every attribute is shared by several combinations - so their relative order was
 * left to the engine, and the front office preselects whichever combination comes first.
 *
 * This asserts the ORDER BY rather than the returned rows on purpose. An unordered query is free to
 * return the right order, and on this database it does most of the time: the same code returned
 * 2,1,4,3,6,5,8,7 on one run and 1,2,3,4,5,6,7,8 on the next. A test that reads the result would
 * therefore pass with the bug present, which is worse than no test at all.
 *
 * The query is read back from the real connection rather than captured through a mocked one.
 * Db::setInstanceForTesting() replaces the connection process-wide, so everything the call reaches
 * while it is installed sees an empty database and can memoise that emptiness in a static that
 * outlives the swap - which is a whole class of cross-test failure this measurement does not need.
 */
class AttributesGroupsOrderTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        if (!Combination::isFeatureActive()) {
            $this->markTestSkipped('getAttributesGroups() returns early when combinations are disabled, so it builds no query.');
        }
    }

    public function testTheOrderByIsTotal(): void
    {
        $sql = $this->queryIssuedByGetAttributesGroups();

        $this->assertStringContainsString('ORDER BY', $sql, 'the query must be ordered at all');
        $this->assertMatchesRegularExpression(
            '/ORDER BY.*id_product_attribute/is',
            $sql,
            'the combination id must take part in the ordering, otherwise rows sharing an attribute are returned in engine order'
        );
    }

    public function testTheTieBreakerComesLastSoTheDocumentedOrderIsKept(): void
    {
        $sql = $this->queryIssuedByGetAttributesGroups();

        preg_match('/ORDER BY(.*)$/is', $sql, $matches);
        $orderBy = $matches[1] ?? '';

        $this->assertNotSame('', trim($orderBy));
        $this->assertLessThan(
            strpos($orderBy, 'id_product_attribute'),
            strpos($orderBy, 'position'),
            'the tie-breaker must come after the positions, or it would override the intended ordering'
        );
    }

    /**
     * Db records every statement it runs in a protected property, so the real call can be measured
     * without standing anything in for the connection.
     */
    private function queryIssuedByGetAttributesGroups(): string
    {
        $product = new Product(1, false, 1);
        $product->getAttributesGroups(1);

        // setAccessible() has been a no-op since PHP 8.1 and is deprecated in 8.5.
        $lastQuery = new ReflectionProperty(Db::class, 'last_query');
        $sql = (string) $lastQuery->getValue(Db::getInstance());

        $this->assertStringContainsString(
            'id_attribute_group',
            $sql,
            'the last statement is not the attributes-groups query, so this test would prove nothing'
        );

        return $sql;
    }
}
