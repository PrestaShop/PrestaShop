<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\VirtualProductFile;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;

class AddVirtualProductFileCommandTest extends TestCase
{
    public function testCombinationIdDefaultsToZero(): void
    {
        $command = new AddVirtualProductFileCommand(1, 'path/to/file', 'display name');

        $this->assertSame(0, $command->getCombinationId());
    }

    public function testSetCombinationIdIsFluentAndStoresValue(): void
    {
        $command = new AddVirtualProductFileCommand(1, 'path/to/file', 'display name');

        $this->assertSame($command, $command->setCombinationId(42));
        $this->assertSame(42, $command->getCombinationId());
    }
}
