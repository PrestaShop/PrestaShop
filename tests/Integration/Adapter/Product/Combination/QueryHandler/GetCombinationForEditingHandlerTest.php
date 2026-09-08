<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Combination\QueryHandler;

use Db;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class GetCombinationForEditingHandlerTest extends KernelTestCase
{
    /**
     * @var object|null
     */
    private $queryBus;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['product_attribute']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['product_attribute']);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->queryBus = self::getContainer()->get('prestashop.core.query_bus');
    }

    /**
     * The product_attribute identifier columns (ean13, upc, mpn, isbn, reference) are
     * nullable in the database, but CombinationDetails is strictly typed to string, so
     * editing a combination whose identifiers are NULL crashed with a TypeError
     * ("Argument #1 ($gtin) must be of type string, null given"). See issue #39988.
     */
    public function testGetCombinationForEditingDoesNotFatalWhenIdentifiersAreNull(): void
    {
        $db = Db::getInstance();
        $combinationId = (int) $db->getValue('SELECT id_product_attribute FROM ' . _DB_PREFIX_ . 'product_attribute');
        self::assertGreaterThan(0, $combinationId, 'Demo data must contain at least one combination.');

        $db->execute('
            UPDATE ' . _DB_PREFIX_ . 'product_attribute
            SET ean13 = NULL, isbn = NULL, mpn = NULL, reference = NULL, upc = NULL
            WHERE id_product_attribute = ' . $combinationId);

        /** @var CombinationForEditing $result */
        $result = $this->queryBus->handle(new GetCombinationForEditing($combinationId, ShopConstraint::allShops()));
        $details = $result->getDetails();

        // NULL identifiers degrade to empty strings instead of fataling.
        self::assertSame('', $details->getGtin());
        self::assertSame('', $details->getEan13());
        self::assertSame('', $details->getIsbn());
        self::assertSame('', $details->getMpn());
        self::assertSame('', $details->getReference());
        self::assertSame('', $details->getUpc());
    }
}
