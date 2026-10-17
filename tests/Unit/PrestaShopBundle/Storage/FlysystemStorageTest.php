<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Storage;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Storage\Exception\StorageOperationException;
use PrestaShopBundle\Storage\FlysystemStorage;

class FlysystemStorageTest extends TestCase
{
    private FlysystemStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new FlysystemStorage(new Filesystem(new InMemoryFilesystemAdapter()));
    }

    public function testItWritesAndReadsAFile(): void
    {
        $this->assertFalse($this->storage->fileExists('p/1/1.jpg'));

        $this->storage->write('p/1/1.jpg', 'image contents');

        $this->assertTrue($this->storage->fileExists('p/1/1.jpg'));
        $this->assertSame('image contents', $this->storage->read('p/1/1.jpg'));
        $this->assertSame(14, $this->storage->fileSize('p/1/1.jpg'));
    }

    public function testItWritesAndReadsAFileAsAStream(): void
    {
        $contents = fopen('php://temp', 'r+b');
        fwrite($contents, 'image contents');
        rewind($contents);

        $this->storage->writeStream('p/1/1.jpg', $contents);

        $this->assertSame('image contents', stream_get_contents($this->storage->readStream('p/1/1.jpg')));
    }

    public function testItOverwritesAnExistingFile(): void
    {
        $this->storage->write('p/1/1.jpg', 'first');
        $this->storage->write('p/1/1.jpg', 'second');

        $this->assertSame('second', $this->storage->read('p/1/1.jpg'));
    }

    public function testItDeletesAFile(): void
    {
        $this->storage->write('p/1/1.jpg', 'image contents');

        $this->storage->delete('p/1/1.jpg');

        $this->assertFalse($this->storage->fileExists('p/1/1.jpg'));
    }

    public function testItCopiesAndMovesAFile(): void
    {
        $this->storage->write('p/1/1.jpg', 'image contents');

        $this->storage->copy('p/1/1.jpg', 'p/2/2.jpg');
        $this->storage->move('p/1/1.jpg', 'p/3/3.jpg');

        $this->assertFalse($this->storage->fileExists('p/1/1.jpg'));
        $this->assertSame('image contents', $this->storage->read('p/2/2.jpg'));
        $this->assertSame('image contents', $this->storage->read('p/3/3.jpg'));
    }

    public function testItDeletesADirectoryAndItsContent(): void
    {
        $this->storage->write('p/1/1.jpg', 'image contents');
        $this->storage->write('p/1/1-large.jpg', 'image contents');

        $this->storage->deleteDirectory('p/1');

        $this->assertFalse($this->storage->fileExists('p/1/1.jpg'));
        $this->assertFalse($this->storage->fileExists('p/1/1-large.jpg'));
    }

    public function testItListsTheFilesOfADirectoryWithoutTheDirectories(): void
    {
        $this->storage->write('p/1.jpg', 'image contents');
        $this->storage->write('p/2.jpg', 'image contents');
        $this->storage->write('p/1/nested.jpg', 'image contents');

        $files = $this->storage->listFiles('p');
        sort($files);

        $this->assertSame(['p/1.jpg', 'p/2.jpg'], $files);
    }

    public function testItListsTheFilesOfADirectoryRecursively(): void
    {
        $this->storage->write('p/1.jpg', 'image contents');
        $this->storage->write('p/1/nested.jpg', 'image contents');

        $files = $this->storage->listFiles('p', true);
        sort($files);

        $this->assertSame(['p/1.jpg', 'p/1/nested.jpg'], $files);
    }

    public function testItWrapsTheExceptionsOfTheBackend(): void
    {
        $this->expectException(StorageOperationException::class);
        $this->expectExceptionMessage('Storage was unable to read file "p/1/missing.jpg"');

        $this->storage->read('p/1/missing.jpg');
    }

    public function testItWrapsTheExceptionOfAStorageWithoutPublicUrl(): void
    {
        $this->storage->write('p/1/1.jpg', 'image contents');

        $this->expectException(StorageOperationException::class);
        $this->expectExceptionMessage('Storage was unable to get the public URL of file "p/1/1.jpg"');

        $this->storage->publicUrl('p/1/1.jpg');
    }
}
