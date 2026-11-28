<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\CmsPageCategory\Query;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryNameForListing;

class GetCmsPageCategoryNameForListingTest extends TestCase
{
    public function testItCarriesAnExplicitCategoryId(): void
    {
        $query = new GetCmsPageCategoryNameForListing(5);

        self::assertNotNull($query->getCmsPageCategoryId());
        self::assertSame(5, $query->getCmsPageCategoryId()->getValue());
    }

    public function testNoArgumentKeepsTheRequestFallback(): void
    {
        self::assertNull((new GetCmsPageCategoryNameForListing())->getCmsPageCategoryId());
    }

    public function testZeroIsRejectedByTheValueObject(): void
    {
        $this->expectException(CmsPageCategoryException::class);

        new GetCmsPageCategoryNameForListing(0);
    }
}
