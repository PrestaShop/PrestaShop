<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Db;
use PHPUnit\Framework\TestCase;
use Product;

/**
 * `getExistingIdsFromIdsOrRefs()` accepts identifiers or references, and CSV import resolves
 * accessories through it. A reference made of digits used to be read as an identifier only, so a
 * catalogue that numbers its references could not import accessories at all.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/10824
 */
class ProductIdsOrRefsTest extends TestCase
{
    private const NUMERIC_REFERENCE = '9876543';
    private const TEXTUAL_REFERENCE = 'REF-CONTROL-1';

    private int $productId;

    private string $originalReference;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productId = (int) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product ORDER BY id_product DESC'
        );
        $this->originalReference = (string) Db::getInstance()->getValue(
            'SELECT reference FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . $this->productId
        );
    }

    protected function tearDown(): void
    {
        Db::getInstance()->update(
            'product',
            ['reference' => pSQL($this->originalReference)],
            'id_product = ' . $this->productId
        );

        parent::tearDown();
    }

    public function testAReferenceMadeOfDigitsResolvesToItsProduct(): void
    {
        $this->giveTheProductTheReference(self::NUMERIC_REFERENCE);

        // Nothing must own that identifier, otherwise the lookup could succeed for the wrong reason.
        $this->assertFalse((bool) Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE id_product = ' . (int) self::NUMERIC_REFERENCE
        ));

        $this->assertContains($this->productId, (array) Product::getExistingIdsFromIdsOrRefs(self::NUMERIC_REFERENCE));
    }

    public function testATextualReferenceStillResolves(): void
    {
        $this->giveTheProductTheReference(self::TEXTUAL_REFERENCE);

        $this->assertContains($this->productId, (array) Product::getExistingIdsFromIdsOrRefs(self::TEXTUAL_REFERENCE));
    }

    public function testAnIdentifierStillResolves(): void
    {
        $this->assertContains($this->productId, (array) Product::getExistingIdsFromIdsOrRefs((string) $this->productId));
    }

    private function giveTheProductTheReference(string $reference): void
    {
        Db::getInstance()->update(
            'product',
            ['reference' => pSQL($reference)],
            'id_product = ' . $this->productId
        );
    }
}
