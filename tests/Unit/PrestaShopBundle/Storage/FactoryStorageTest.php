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
use PrestaShop\PrestaShop\Core\Storage\Exception\StorageNotFoundException;
use PrestaShop\PrestaShop\Core\Storage\StorageSettings;
use PrestaShopBundle\Storage\FactoryStorage;
use PrestaShopBundle\Storage\FlysystemStorage;
use Symfony\Component\DependencyInjection\ServiceLocator;

class FactoryStorageTest extends TestCase
{
    private FactoryStorage $factory;

    protected function setUp(): void
    {
        $this->factory = new FactoryStorage(new ServiceLocator([
            StorageSettings::STORAGE_DEFAULT => fn () => $this->createStorage(),
            StorageSettings::STORAGE_IMAGES => fn () => $this->createStorage(),
        ]));
    }

    public function testItReturnsAStorageByName(): void
    {
        $this->assertInstanceOf(FlysystemStorage::class, $this->factory->get(StorageSettings::STORAGE_IMAGES));
    }

    public function testItTellsWhichStoragesAreRegistered(): void
    {
        $this->assertTrue($this->factory->has(StorageSettings::STORAGE_IMAGES));
        $this->assertFalse($this->factory->has('invoices'));
        $this->assertSame(
            [StorageSettings::STORAGE_DEFAULT, StorageSettings::STORAGE_IMAGES],
            $this->factory->getNames()
        );
    }

    public function testItThrowsAnExceptionListingTheKnownStoragesForAnUnknownName(): void
    {
        $this->expectException(StorageNotFoundException::class);
        $this->expectExceptionMessage('Storage "invoices" was not found, known storages are: default, images.');

        $this->factory->get('invoices');
    }

    private function createStorage(): FlysystemStorage
    {
        return new FlysystemStorage(new Filesystem(new InMemoryFilesystemAdapter()));
    }
}
