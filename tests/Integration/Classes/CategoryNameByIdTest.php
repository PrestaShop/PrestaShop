<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Category;
use Configuration;
use Db;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Category::getNameById() memoizes per request because a product listing asks for the same handful of
 * categories once per product. The memo is observed the only way a memo can be observed without
 * counting queries: change the row underneath it and see that the answer does not follow.
 */
class CategoryNameByIdTest extends KernelTestCase
{
    /**
     * @var int
     */
    private $idLang;

    /**
     * @var int
     */
    private $idShop;

    /**
     * @var array<int, string> original names, restored in tearDown
     */
    private $originalNames = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->idShop = (int) Configuration::get('PS_SHOP_DEFAULT');
    }

    protected function tearDown(): void
    {
        foreach ($this->originalNames as $idCategory => $name) {
            $this->writeName($idCategory, $name);
        }

        parent::tearDown();
    }

    private function readName(int $idCategory): string
    {
        return (string) Db::getInstance()->getValue(
            'SELECT name FROM ' . _DB_PREFIX_ . 'category_lang'
            . ' WHERE id_category = ' . $idCategory
            . ' AND id_lang = ' . $this->idLang
            . ' AND id_shop = ' . $this->idShop
        );
    }

    private function writeName(int $idCategory, string $name): void
    {
        Db::getInstance()->update(
            'category_lang',
            ['name' => pSQL($name)],
            'id_category = ' . $idCategory . ' AND id_lang = ' . $this->idLang
                . ' AND id_shop = ' . $this->idShop
        );
    }

    /**
     * @return int[] two category ids that have a name in the default language and shop
     */
    private function getTwoCategoryIds(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_category FROM ' . _DB_PREFIX_ . 'category_lang'
            . ' WHERE id_lang = ' . $this->idLang
            . ' AND id_shop = ' . $this->idShop
            . " AND name <> '' ORDER BY id_category LIMIT 2"
        );
        $ids = array_map('intval', array_column((array) $rows, 'id_category'));
        self::assertCount(2, $ids, 'the fixture needs two named categories');

        return $ids;
    }

    public function testTheNameIsReadOnlyOnceForTheSameCategory(): void
    {
        [$idCategory] = $this->getTwoCategoryIds();
        $this->originalNames[$idCategory] = $this->readName($idCategory);

        $first = Category::getNameById($idCategory, $this->idLang, $this->idShop);
        self::assertSame($this->originalNames[$idCategory], $first);

        // Only a second query would see this.
        $this->writeName($idCategory, $this->originalNames[$idCategory] . ' CHANGED');

        self::assertSame(
            $first,
            Category::getNameById($idCategory, $this->idLang, $this->idShop),
            'the name was read from the database a second time'
        );
    }

    public function testEachCategoryKeepsItsOwnName(): void
    {
        [$first, $second] = $this->getTwoCategoryIds();

        // Guards the memo key: one entry per category, not one entry for all of them.
        self::assertNotSame(
            Category::getNameById($first, $this->idLang, $this->idShop),
            Category::getNameById($second, $this->idLang, $this->idShop)
        );
    }

    public function testAnInvalidIdIsRejectedWithoutQuerying(): void
    {
        self::assertFalse(Category::getNameById(0, $this->idLang, $this->idShop));
        self::assertFalse(Category::getNameById(-1, $this->idLang, $this->idShop));
        self::assertFalse(Category::getNameById(1, 0, $this->idShop));
    }
}
