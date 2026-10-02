<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Stock;

use Db;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use StockAvailable;

class StockAvailableDefinitionTest extends TestCase
{
    /**
     * The reported defect: physical_quantity and reserved_quantity have been columns since 1.7.2 and
     * were never added to the model, so the webservice could not see them. Comparing the definition
     * against the table catches the next column that is added without one.
     */
    public function testTheDefinitionCoversEveryColumnOfTheTable(): void
    {
        $table = _DB_PREFIX_ . StockAvailable::$definition['table'];

        $columns = array_column(
            Db::getInstance()->executeS('SHOW COLUMNS FROM `' . $table . '`') ?: [],
            'Field'
        );

        $this->assertNotEmpty($columns, 'the table must be readable for this test to mean anything');

        $known = array_merge(
            array_keys(StockAvailable::$definition['fields']),
            [StockAvailable::$definition['primary']]
        );

        $this->assertSame(
            [],
            array_values(array_diff($columns, $known)),
            'every column of ' . $table . ' should be described by the object model'
        );
    }

    /**
     * Both are derived by the stock system, so the webservice may read them and must not write them,
     * the way Category::level_depth is handled.
     */
    public function testTheDerivedQuantitiesAreNotWritableThroughTheWebservice(): void
    {
        $stockAvailable = new StockAvailable();

        $reflection = new ReflectionProperty(StockAvailable::class, 'webserviceParameters');
        $reflection->setAccessible(true);
        $parameters = $reflection->getValue($stockAvailable);

        foreach (['physical_quantity', 'reserved_quantity'] as $field) {
            $this->assertArrayHasKey($field, $parameters['fields'], $field . ' should be exposed');
            $this->assertFalse(
                $parameters['fields'][$field]['setter'],
                $field . ' is maintained by the stock system and must not be settable'
            );
        }
    }
}
