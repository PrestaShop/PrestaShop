<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\BusinessEntity\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command\BulkDeleteBusinessEntityCommand;

class BulkDeleteBusinessEntityCommandTest extends TestCase
{
    public function testItExposesBusinessEntityIdsViaGetter(): void
    {
        $command = new BulkDeleteBusinessEntityCommand([4, 8, 15]);

        $this->assertSame([4, 8, 15], $command->getBusinessEntityIds());
    }
}
