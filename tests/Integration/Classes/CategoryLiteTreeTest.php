<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Category;
use Configuration;
use Context;
use Customer;
use Db;
use FrontController;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\DatabaseDump;

class CategoryLiteTreeTest extends TestCase
{
    use ContextMockerTrait;

    private const CUSTOMER_GROUP_ID = 3;

    /**
     * @var array<string, int>
     */
    private $categoryIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
        $this->resetCurrentCustomerGroups();

        // Top
        // ├── B (position 0)
        // ├── A (position 1)
        // │   ├── A1 ── A1a
        // │   └── A2
        // ├── C (inactive) ── C1
        // └── D (customer group only)
        $this->createCategory('Top', (int) Configuration::get('PS_HOME_CATEGORY'));
        $this->createCategory('A', $this->categoryIds['Top']);
        $this->createCategory('B', $this->categoryIds['Top']);
        $this->createCategory('A1', $this->categoryIds['A'], true, null, '<p>Some <b>bold</b> description</p>');
        $this->createCategory('A1a', $this->categoryIds['A1']);
        $this->createCategory('A2', $this->categoryIds['A']);
        $this->createCategory('C', $this->categoryIds['Top'], false);
        $this->createCategory('C1', $this->categoryIds['C']);
        $this->createCategory('D', $this->categoryIds['Top'], true, [self::CUSTOMER_GROUP_ID]);
        Db::getInstance()->update('category_shop', ['position' => 0], 'id_category = ' . $this->categoryIds['B']);
        Db::getInstance()->update('category_shop', ['position' => 1], 'id_category = ' . $this->categoryIds['A']);
    }

    protected function tearDown(): void
    {
        $this->resetCurrentCustomerGroups();
        DatabaseDump::restoreTables(['category', 'category_group', 'category_lang', 'category_shop']);
        parent::tearDown();
    }

    public function testSitemapTreeOfAVisitor(): void
    {
        $tree = $this->getTopCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap');

        $this->assertSame(
            ['Top' => ['B' => [], 'A' => ['A1' => ['A1a' => []], 'A2' => []]]],
            $this->getLabels($tree)
        );
        $this->assertSame('category-page-' . $this->categoryIds['A1'], $tree['children'][1]['children'][0]['id']);
        $this->assertSame(
            Context::getContext()->link->getCategoryLink($this->categoryIds['A1'], 'a1'),
            $tree['children'][1]['children'][0]['url']
        );
    }

    public function testSitemapTreeOfACustomerIncludesItsGroupCategories(): void
    {
        $customer = new Customer();
        $customer->firstname = 'Lite';
        $customer->lastname = 'Tree';
        $customer->email = 'lite.tree@example.com';
        $customer->passwd = '$2y$10$Z3XcYEAhYuSlXv5j4gXVv.Wc5Lr8lGSQZPTu7tZ8Vwo9gECkq0G2e';
        $customer->id_default_group = self::CUSTOMER_GROUP_ID;
        $customer->add();
        $customer->updateGroup([self::CUSTOMER_GROUP_ID]);
        Context::getContext()->customer = $customer;

        try {
            $tree = $this->getTopCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap');
            $this->assertSame(
                ['Top' => ['B' => [], 'A' => ['A1' => ['A1a' => []], 'A2' => []], 'D' => []]],
                $this->getLabels($tree)
            );
        } finally {
            $customer->delete();
        }
    }

    public function testMaxDepthAndExcludedCategories(): void
    {
        $this->assertSame(
            ['Top' => ['B' => [], 'A' => []]],
            $this->getLabels($this->getTopCategory()->recurseLiteCategTree(1, 0, null, null, 'sitemap'))
        );
        $this->assertSame(
            ['Top' => ['B' => [], 'A' => ['A1' => []]]],
            $this->getLabels($this->getTopCategory()->recurseLiteCategTree(2, 0, null, [$this->categoryIds['A2']], 'sitemap'))
        );
    }

    public function testSiblingsWithTheSamePositionAreSortedById(): void
    {
        Db::getInstance()->update('category_shop', ['position' => 0], 'id_category IN (' . $this->categoryIds['A1'] . ', ' . $this->categoryIds['A2'] . ')');

        $this->assertSame(
            ['Top' => ['B' => [], 'A' => ['A1' => ['A1a' => []], 'A2' => []]]],
            $this->getLabels($this->getTopCategory()->recurseLiteCategTree(0, 0, null, null, 'sitemap'))
        );
    }

    public function testUnknownLanguageFallsBackToTheDefaultOne(): void
    {
        $defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');

        $this->assertSame(
            $this->getTopCategory()->recurseLiteCategTree(0, 0, $defaultLanguageId, null, 'default'),
            $this->getTopCategory()->recurseLiteCategTree(0, 0, 999, null, 'default')
        );
    }

    public function testDefaultFormat(): void
    {
        $tree = $this->getTopCategory()->recurseLiteCategTree(0, 0, null, null, 'default');
        $categoryA1 = $tree['children'][1]['children'][0];

        $this->assertSame(['id', 'link', 'name', 'desc', 'children'], array_keys($categoryA1));
        $this->assertSame($this->categoryIds['A1'], $categoryA1['id']);
        $this->assertSame('A1', $categoryA1['name']);
        $this->assertSame(Category::getDescriptionClean('<p>Some <b>bold</b> description</p>'), $categoryA1['desc']);
        $this->assertSame(Context::getContext()->link->getCategoryLink($this->categoryIds['A1'], 'a1'), $categoryA1['link']);
        $this->assertSame('A1a', $categoryA1['children'][0]['name']);
    }

    /**
     * @param int[]|null $groupIds
     */
    private function createCategory(string $name, int $parentId, bool $active = true, ?array $groupIds = null, string $description = ''): void
    {
        $languageIds = Language::getIDs(false);
        $category = new Category();
        $category->name = array_fill_keys($languageIds, $name);
        $category->link_rewrite = array_fill_keys($languageIds, strtolower($name));
        $category->description = array_fill_keys($languageIds, $description);
        $category->id_parent = $parentId;
        $category->active = $active;
        $category->groupBox = $groupIds ?? [
            (int) Configuration::get('PS_UNIDENTIFIED_GROUP'),
            (int) Configuration::get('PS_GUEST_GROUP'),
            self::CUSTOMER_GROUP_ID,
        ];
        $category->add();
        $this->categoryIds[$name] = (int) $category->id;
    }

    private function getTopCategory(): Category
    {
        return new Category($this->categoryIds['Top'], (int) Configuration::get('PS_LANG_DEFAULT'));
    }

    /**
     * @param array<string, mixed> $node
     *
     * @return array<string, array>
     */
    private function getLabels(array $node): array
    {
        $children = [];
        foreach ($node['children'] as $child) {
            $children += $this->getLabels($child);
        }

        return [$node['label'] => $children];
    }

    private function resetCurrentCustomerGroups(): void
    {
        $property = new ReflectionProperty(FrontController::class, 'currentCustomerGroups');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }
}
