<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\CmsPageCategory\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\EditableCmsPageCategory;

class EditableCmsPageCategoryTest extends TestCase
{
    public function testTheRootCategoryHasNoParent(): void
    {
        $category = new EditableCmsPageCategory(['Home'], true, 0, [], [], [], ['home'], [1]);

        $this->assertNull($category->getParentId());
    }

    public function testAChildCategoryHasItsParent(): void
    {
        $category = new EditableCmsPageCategory(['Child'], true, 1, [], [], [], ['child'], [1]);

        $this->assertSame(1, $category->getParentId()->getValue());
    }
}
