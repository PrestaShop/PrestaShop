<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Classes\Category;

use Cache;
use Category;
use Context;
use PHPUnit\Framework\TestCase;
use Shop;

class CategoryGroupCacheTest extends TestCase
{
    private const HOME_CATEGORY_ID = 2;

    private $originalShop;

    protected function setUp(): void
    {
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', 'ps_');
        }

        $context = Context::getContext();
        $this->originalShop = $context->shop;

        $shop = new Shop();
        $shop->id_category = self::HOME_CATEGORY_ID;
        $context->shop = $shop;
    }

    protected function tearDown(): void
    {
        Context::getContext()->shop = $this->originalShop;
        Cache::clean('Category::getGroups_*');
    }

    public function testSetNewGroupForHomeClearsGroupsCache(): void
    {
        $cacheId = 'Category::getGroups_' . self::HOME_CATEGORY_ID;
        Cache::store($cacheId, [1, 2, 3]);

        Category::setNewGroupForHome(4);

        $this->assertFalse(Cache::isStored($cacheId));
    }
}
