<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\OrderReturn\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

class OrderReturnIdTest extends TestCase
{
    public function testConstruct(): void
    {
        $orderReturnId = new OrderReturnId(5);
        $this->assertEquals(5, $orderReturnId->getValue());

        $this->expectException(OrderReturnConstraintException::class);
        new OrderReturnId(-5);
    }
}
