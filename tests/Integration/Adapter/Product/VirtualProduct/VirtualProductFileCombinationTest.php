<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\VirtualProduct;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command\AddVirtualProductFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception\VirtualProductFileNotFoundException;
use ProductDownload as VirtualProductFile;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DummyFileUploader;

/**
 * Combination-scoped virtual product file persistence.
 *
 * Creates a virtual_combinations product with two combinations, adds a distinct file
 * to each combination, and asserts that:
 *  - getIdFromCombination returns a different id_product_download per combination;
 *  - the product-level lookup (findByProductId / id_product_attribute = 0) finds nothing;
 *  - findAllByProductId returns every combination row.
 *
 * @group integration
 */
class VirtualProductFileCombinationTest extends KernelTestCase
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
     * @var int
     */
    private $productId;

    /**
     * @var int[]
     */
    private $combinationIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();
        $this->repository = $container->get(VirtualProductFileRepository::class);
        $this->commandBus = $container->get('prestashop.core.command_bus');

        $this->productId = $this->createVirtualCombinationsProduct();
        $this->combinationIds = [
            $this->createCombination($this->productId),
            $this->createCombination($this->productId),
        ];
    }

    protected function tearDown(): void
    {
        if ($this->productId) {
            // Remove generated download rows + product to keep the DB clean
            foreach ($this->repository->findAllByProductId(new ProductId($this->productId)) as $file) {
                $file->delete();
            }
            foreach ($this->combinationIds as $combinationId) {
                (new Combination($combinationId))->delete();
            }
        }

        parent::tearDown();
    }

    public function testAddDistinctFilePerCombination(): void
    {
        [$firstCombinationId, $secondCombinationId] = $this->combinationIds;

        $this->addFileToCombination('first display name', $firstCombinationId);
        $this->addFileToCombination('second display name', $secondCombinationId);

        $firstFileId = (int) VirtualProductFile::getIdFromCombination($this->productId, $firstCombinationId, false);
        $secondFileId = (int) VirtualProductFile::getIdFromCombination($this->productId, $secondCombinationId, false);

        $this->assertGreaterThan(0, $firstFileId);
        $this->assertGreaterThan(0, $secondFileId);
        $this->assertNotEquals($firstFileId, $secondFileId);

        // No product-level (id_product_attribute = 0) file should exist
        $this->expectException(VirtualProductFileNotFoundException::class);
        $this->repository->findByProductId(new ProductId($this->productId));
    }

    public function testFindAllByProductIdReturnsEveryCombinationFile(): void
    {
        [$firstCombinationId, $secondCombinationId] = $this->combinationIds;

        $this->addFileToCombination('first display name', $firstCombinationId);
        $this->addFileToCombination('second display name', $secondCombinationId);

        $files = $this->repository->findAllByProductId(new ProductId($this->productId));

        $this->assertCount(2, $files);
    }

    private function addFileToCombination(string $displayName, int $combinationId): void
    {
        $filePath = DummyFileUploader::upload('app_icon.png');
        $command = new AddVirtualProductFileCommand($this->productId, $filePath, $displayName);
        $command->setCombinationId($combinationId);

        $this->commandBus->handle($command);
    }

    private function createVirtualCombinationsProduct(): int
    {
        /** @var ProductId $productId */
        $productId = $this->commandBus->handle(new AddProductCommand(
            ProductType::TYPE_VIRTUAL_COMBINATIONS,
            (int) \Context::getContext()->shop->id,
            ['1' => 'Virtual combinations product']
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
