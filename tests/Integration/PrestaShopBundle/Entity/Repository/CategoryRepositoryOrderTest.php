<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Context;
use Db;
use Employee;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The category list behind the stock filter was ordered by parent alone, so siblings came back in
 * whatever order the storage engine happened to return - usually by id, which is not the order the
 * merchant arranged them in.
 */
class CategoryRepositoryOrderTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['category'];
    private const PARENT_ID = 2;

    public static function tearDownAfterClass(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDownAfterClass();
    }

    public function testSiblingsComeBackInTheOrderTheMerchantArrangedThem(): void
    {
        self::bootKernel();

        // The repository reads the language and shop from the context when it is built.
        $context = Context::getContext();
        $context->employee = new Employee(1);
        $context->shop = new Shop(1);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);

        $db = Db::getInstance();

        $siblingIds = array_map(
            'intval',
            array_column(
                $db->executeS(
                    'SELECT id_category FROM ' . _DB_PREFIX_ . 'category
                     WHERE id_parent = ' . self::PARENT_ID . ' ORDER BY id_category ASC'
                ) ?: [],
                'id_category'
            )
        );
        self::assertGreaterThanOrEqual(2, count($siblingIds), 'the fixture needs at least two sibling categories');

        // Arrange them against their id order, so that ordering by id cannot pass by accident.
        $expected = array_reverse($siblingIds);
        foreach ($expected as $position => $categoryId) {
            $db->execute(
                'UPDATE ' . _DB_PREFIX_ . 'category SET position = ' . (int) $position
                . ' WHERE id_category = ' . $categoryId
            );
        }

        $categories = self::getContainer()->get('prestashop.core.api.category.repository')->getCategories();

        $returned = [];
        foreach ($categories as $category) {
            if ((int) $category['id_parent'] === self::PARENT_ID) {
                $returned[] = (int) $category['id_category'];
            }
        }

        self::assertSame($expected, $returned, 'the siblings did not come back in their configured order');
    }
}
