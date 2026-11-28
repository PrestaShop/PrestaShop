<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\ImageSettings\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsException;

class RegenerateThumbnailsCommandTest extends TestCase
{
    public function testWorkersDefaultToASingleProcess(): void
    {
        $command = new RegenerateThumbnailsCommand('products', 0, false);

        $this->assertSame(1, $command->getWorkerCount());
        $this->assertNull($command->getWorkerIndex());
    }

    /**
     * @dataProvider invalidWorkersProvider
     */
    public function testInvalidWorkers(int $workerCount, ?int $workerIndex): void
    {
        $this->expectException(RegenerateThumbnailsException::class);

        new RegenerateThumbnailsCommand('products', 0, false, $workerCount, $workerIndex);
    }

    public static function invalidWorkersProvider(): iterable
    {
        yield 'no worker' => [0, null];
        yield 'negative index' => [2, -1];
        yield 'index out of range' => [2, 2];
    }
}
