<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class ProductTest extends TestCase
{
    use ContextMockerTrait;

    private const NUMERIC_REFERENCE = '9876543';

    /**
     * @var array<int, string>
     */
    private array $originalReferences = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    protected function tearDown(): void
    {
        foreach ($this->originalReferences as $productId => $reference) {
            Db::getInstance()->update('product', ['reference' => pSQL($reference)], 'id_product = ' . $productId);
        }
        $this->originalReferences = [];
        parent::tearDown();
    }

    public function testSaveActiveRecordStyle(): void
    {
        $product = new Product(null, false, 1);
        $product->name = 'A Product';
        $product->price = 42.42;
        $product->link_rewrite = 'a-product';
        $this->assertTrue($product->save());
    }

    public function testAReferenceMadeOfDigitsResolvesToItsProduct(): void
    {
        $this->setReference(1, self::NUMERIC_REFERENCE);
        $this->assertFalse((bool) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . (int) self::NUMERIC_REFERENCE
        ));

        $this->assertSame([1], Product::getExistingIdsFromIdsOrRefs(self::NUMERIC_REFERENCE));
    }

    public function testATextualReferenceAndAnIdStillResolve(): void
    {
        $this->setReference(1, 'REF-CONTROL-1');

        $this->assertEqualsCanonicalizing([1, 2], Product::getExistingIdsFromIdsOrRefs(['REF-CONTROL-1', '2']));
    }

    public function testAReferenceMatchesWhateverItsCase(): void
    {
        $this->setReference(1, 'REF-CONTROL-1');

        $this->assertSame([1], Product::getExistingIdsFromIdsOrRefs('ref-control-1'));
    }

    public function testAValueThatIsAnIdAndAnotherProductReferenceResolvesToTheId(): void
    {
        $this->setReference(2, '1');

        $this->assertSame([1], Product::getExistingIdsFromIdsOrRefs('1'));
    }

    private function setReference(int $productId, string $reference): void
    {
        $this->originalReferences[$productId] ??= (string) Db::getInstance()->getValue(
            'SELECT reference FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . $productId
        );
        Db::getInstance()->update('product', ['reference' => pSQL($reference)], 'id_product = ' . $productId);
    }
}
