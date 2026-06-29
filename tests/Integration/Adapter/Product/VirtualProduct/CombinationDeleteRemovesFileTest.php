<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\VirtualProduct;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkDeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use ProductDownload as VirtualProductFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DummyFileUploader;

/**
 * Deleting a combination must remove its virtual file: both the product_download
 * row (getIdFromCombination => 0) and the file on disk.
 *
 * Covers:
 *  - single-combination deletion (DeleteCombinationCommand);
 *  - deleting a combination that has NO file is a clean no-op;
 *  - bulk-deleting combinations cleans up every file.
 *
 * @group integration
 */
class CombinationDeleteRemovesFileTest extends KernelTestCase
{
    /**
     * @var VirtualProductFileRepository
     */
    private $repository;

    /**
     * @var object command bus
     */
    private $commandBus;

    /**
     * @var int|null
     */
    private $productId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();
        $this->repository = $container->get(VirtualProductFileRepository::class);
        $this->commandBus = $container->get('prestashop.core.command_bus');

        $this->productId = $this->createCombinationsProduct();
    }

    protected function tearDown(): void
    {
        if ($this->productId) {
            try {
                $this->commandBus->handle(new DeleteProductCommand($this->productId, ShopConstraint::allShops()));
            } catch (\Throwable) {
                // Best effort cleanup
            }
            $this->productId = null;
        }

        parent::tearDown();
    }

    public function testDeletingCombinationRemovesItsFileRowAndDiskFile(): void
    {
        $combinationId = $this->createCombination($this->productId);
        $this->addFileToCombination('display name', $combinationId);

        $fileId = (int) VirtualProductFile::getIdFromCombination($this->productId, $combinationId, false);
        $this->assertGreaterThan(0, $fileId);

        $filePath = _PS_DOWNLOAD_DIR_ . (new VirtualProductFile($fileId))->filename;
        $this->assertFileExists($filePath);

        $this->commandBus->handle(new DeleteCombinationCommand($combinationId, ShopConstraint::allShops()));

        // product_download row gone
        $this->assertSame(0, (int) VirtualProductFile::getIdFromCombination($this->productId, $combinationId, false));

        // file removed from disk
        $this->assertFileDoesNotExist($filePath);
    }

    public function testDeletingCombinationWithoutFileIsNoOp(): void
    {
        $combinationId = $this->createCombination($this->productId);

        // Must not throw despite the combination having no virtual file
        $this->commandBus->handle(new DeleteCombinationCommand($combinationId, ShopConstraint::allShops()));

        $this->assertSame(0, (int) VirtualProductFile::getIdFromCombination($this->productId, $combinationId, false));
    }

    public function testBulkDeletingCombinationsRemovesEveryFile(): void
    {
        $firstCombinationId = $this->createCombination($this->productId);
        $secondCombinationId = $this->createCombination($this->productId);
        $this->addFileToCombination('first', $firstCombinationId);
        $this->addFileToCombination('second', $secondCombinationId);

        $this->assertCount(2, $this->repository->findAllByProductId(new ProductId($this->productId)));

        $this->commandBus->handle(new BulkDeleteCombinationCommand(
            $this->productId,
            [$firstCombinationId, $secondCombinationId],
            ShopConstraint::allShops()
        ));

        $this->assertSame(0, (int) VirtualProductFile::getIdFromCombination($this->productId, $firstCombinationId, false));
        $this->assertSame(0, (int) VirtualProductFile::getIdFromCombination($this->productId, $secondCombinationId, false));

        $this->expectException(VirtualProductFileNotFoundException::class);
        $this->repository->findByCombinationId(new ProductId($this->productId), $firstCombinationId);
    }

    private function addFileToCombination(string $displayName, int $combinationId): void
    {
        $filePath = DummyFileUploader::upload('app_icon.png');
        $command = new AddVirtualProductFileCommand($this->productId, $filePath, $displayName);
        $command->setCombinationId($combinationId);

        $this->commandBus->handle($command);
    }

    private function createCombinationsProduct(): int
    {
        /** @var ProductId $productId */
        $productId = $this->commandBus->handle(new AddProductCommand(
            ProductType::TYPE_COMBINATIONS,
            (int) \Context::getContext()->shop->id,
            ['1' => 'Combinations product']
        ));

        return $productId->getValue();
    }

    private function createCombination(int $productId): int
    {
        $combination = new Combination();
        $combination->id_product = $productId;
        $combination->add();

        return (int) $combination->id;
    }
}
