<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\OrderReturn\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult\OrderReturnForEditing;

class OrderReturnForEditingReturnTest extends TestCase
{
    public function testGetCustomerFullName(): void
    {
        $orderReturnForEditing = new OrderReturnForEditing(
            1,
            1,
            'John',
            'Doe',
            1,
            new DateTimeImmutable(),
            1,
            ''
        );

        $this->assertEquals('John Doe', $orderReturnForEditing->getCustomerFullName());
    }
}
