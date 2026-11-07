<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Category\Repository;

use Db;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CategoryBreadcrumbPartsTest extends KernelTestCase
{
    private const LANGUAGE_ID = 1;

    private CategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->repository = self::getContainer()->get(CategoryRepository::class);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['category_lang']);
        parent::tearDown();
    }

    public function testItMatchesTheBreadcrumbOfEachCategory(): void
    {
        $categoryIds = array_map(
            static fn (array $row): CategoryId => new CategoryId((int) $row['id_category']),
            Db::getInstance()->executeS('SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE level_depth >= 1')
        );

        $breadcrumbs = $this->repository->getBreadcrumbPartsForCategories($categoryIds, new ShopId(1), new LanguageId(self::LANGUAGE_ID));

        $this->assertCount(count($categoryIds), $breadcrumbs);
        foreach ($categoryIds as $categoryId) {
            $this->assertSame(
                $this->repository->getBreadcrumbParts($categoryId, new LanguageId(self::LANGUAGE_ID)),
                $breadcrumbs[$categoryId->getValue()]
            );
        }
    }

    public function testTheNamesComeFromTheRequestedShop(): void
    {
        $categoryId = (int) Db::getInstance()->getValue(
            'SELECT id_category FROM ' . _DB_PREFIX_ . 'category WHERE level_depth = 3 ORDER BY id_category'
        );
        // The same categories under other names in a second shop
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'category_lang (id_category, id_shop, id_lang, name, description, link_rewrite, meta_title, meta_description)
             SELECT id_category, 2, id_lang, CONCAT(\'Shop 2 \', name), description, link_rewrite, meta_title, meta_description
             FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_shop = 1'
        );

        $shopOne = $this->repository->getBreadcrumbPartsForCategories([new CategoryId($categoryId)], new ShopId(1), new LanguageId(self::LANGUAGE_ID))[$categoryId];
        $shopTwo = $this->repository->getBreadcrumbPartsForCategories([new CategoryId($categoryId)], new ShopId(2), new LanguageId(self::LANGUAGE_ID))[$categoryId];

        $this->assertSame(array_map(static fn (string $name): string => 'Shop 2 ' . $name, $shopOne), $shopTwo);
    }
}
