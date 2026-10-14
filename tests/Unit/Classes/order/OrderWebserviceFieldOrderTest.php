<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Classes\order;

use Order;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * WebserviceRequest assigns null to every declared field the request left out, walking them in
 * declaration order. The status change generates the invoice, so it has to run after the fields it
 * fills, or the save that follows writes those nulls over them.
 */
class OrderWebserviceFieldOrderTest extends TestCase
{
    private const FILLED_BY_THE_STATUS_CHANGE = [
        'invoice_number',
        'invoice_date',
        'delivery_number',
        'delivery_date',
    ];

    public function testTheStatusChangeIsDeclaredAfterEveryFieldItFills(): void
    {
        $fields = array_keys($this->getWebserviceFields());
        $statusPosition = array_search('current_state', $fields, true);

        $this->assertNotFalse($statusPosition, 'current_state is no longer a webservice field');

        foreach (self::FILLED_BY_THE_STATUS_CHANGE as $field) {
            $position = array_search($field, $fields, true);
            $this->assertNotFalse($position, sprintf('"%s" is no longer a webservice field', $field));
            $this->assertLessThan(
                $statusPosition,
                $position,
                sprintf('"%s" is declared after current_state, so a status change would null it', $field)
            );
        }
    }

    private function getWebserviceFields(): array
    {
        $property = new ReflectionProperty(Order::class, 'webserviceParameters');
        $property->setAccessible(true);

        return $property->getValue((new ReflectionClass(Order::class))->newInstanceWithoutConstructor())['fields'];
    }
}
