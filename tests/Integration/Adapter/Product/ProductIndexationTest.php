<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product;

use Configuration;
use Context;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The BO product form writes through UpdateProductCommand and reads back through
 * GetProductForEditing, so both directions have to carry the flag.
 */
class ProductIndexationTest extends KernelTestCase
{
    private $commandBus;
    private $queryBus;
    private int $productId;
    private int $shopId;
    private bool $originalValue;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::getContainer();
        Context::getContext()->container = $container;
        $this->commandBus = $container->get('prestashop.core.command_bus');
        $this->queryBus = $container->get('prestashop.core.query_bus');

        $this->shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        $this->productId = (int) Db::getInstance()->getValue(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'product` ORDER BY `id_product` ASC'
        );
        $this->originalValue = (bool) (new Product($this->productId, false, null, $this->shopId))->indexation;
    }

    protected function tearDown(): void
    {
        $product = new Product($this->productId, false, null, $this->shopId);
        $product->indexation = $this->originalValue;
        $product->save();
        parent::tearDown();
    }

    public function testTheCommandWritesIndexationToTheShopRow(): void
    {
        $command = new UpdateProductCommand($this->productId, ShopConstraint::shop($this->shopId));
        $command->setIndexation(false);
        $this->commandBus->handle($command);

        $stored = Db::getInstance()->getValue(
            'SELECT `indexation` FROM `' . _DB_PREFIX_ . 'product_shop` WHERE `id_product` = '
            . $this->productId . ' AND `id_shop` = ' . $this->shopId
        );
        $this->assertSame('0', (string) $stored);
    }

    public function testTheQueryReadsIndexationBackForTheForm(): void
    {
        $command = new UpdateProductCommand($this->productId, ShopConstraint::shop($this->shopId));
        $command->setIndexation(false);
        $this->commandBus->handle($command);

        $this->assertFalse($this->readBack());

        $command = new UpdateProductCommand($this->productId, ShopConstraint::shop($this->shopId));
        $command->setIndexation(true);
        $this->commandBus->handle($command);

        $this->assertTrue($this->readBack());
    }

    public function testACommandThatDoesNotTouchIndexationLeavesItAlone(): void
    {
        $command = new UpdateProductCommand($this->productId, ShopConstraint::shop($this->shopId));
        $command->setIndexation(false);
        $this->commandBus->handle($command);

        // A command carrying no indexation at all must not reset the stored value.
        $this->commandBus->handle(new UpdateProductCommand($this->productId, ShopConstraint::shop($this->shopId)));

        $this->assertFalse($this->readBack(), 'An unrelated update must not silently re-enable indexation.');
    }

    private function readBack(): bool
    {
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->queryBus->handle(new GetProductForEditing(
            $this->productId,
            ShopConstraint::shop($this->shopId),
            (int) Configuration::get('PS_LANG_DEFAULT')
        ));

        return $productForEditing->getProductSeoOptions()->getIndexation();
    }
}
