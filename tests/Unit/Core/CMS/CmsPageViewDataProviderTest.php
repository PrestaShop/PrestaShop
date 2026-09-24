<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\CMS;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CMS\CmsPageViewDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoriesForBreadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageParentCategoryIdForRedirection;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\QueryResult\Breadcrumb;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

class CmsPageViewDataProviderTest extends TestCase
{
    private const ROOT_ID = CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID;
    private const CHILD_ID = 5;
    private const PARENT_ID = 3;

    public function testItReportsTheCategoryOneLevelAboveTheOneBeingListed(): void
    {
        $provider = new CmsPageViewDataProvider($this->mockQueryBus());

        $view = $provider->getView(self::CHILD_ID);

        $this->assertSame(self::PARENT_ID, $view['parent_category_id']);
        $this->assertSame(self::ROOT_ID, $view['root_category_id']);
    }

    public function testItStaysOnTheRootWhenTheRootIsBeingListed(): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        // The root has nothing above it, so the parent must not even be looked up
        $queryBus->method('handle')->willReturnCallback(
            function ($query) {
                $this->assertNotInstanceOf(GetCmsPageParentCategoryIdForRedirection::class, $query);

                return new Breadcrumb([]);
            }
        );

        $view = (new CmsPageViewDataProvider($queryBus))->getView(self::ROOT_ID);

        $this->assertSame(self::ROOT_ID, $view['parent_category_id']);
    }

    public function testItFallsBackToTheRootWhenTheLevelAboveCannotBeResolved(): void
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        // The breadcrumb still answers, only the parent lookup fails: a listing that can be
        // drawn must not lose its back link over it
        $queryBus->method('handle')->willReturnCallback(
            function ($query) {
                if ($query instanceof GetCmsPageParentCategoryIdForRedirection) {
                    throw new CmsPageCategoryException('parent is unreachable');
                }

                return new Breadcrumb([]);
            }
        );

        $view = (new CmsPageViewDataProvider($queryBus))->getView(self::CHILD_ID);

        $this->assertSame(self::ROOT_ID, $view['parent_category_id']);
    }

    private function mockQueryBus(): CommandBusInterface
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus->method('handle')->willReturnCallback(
            function ($query) {
                if ($query instanceof GetCmsPageParentCategoryIdForRedirection) {
                    return new CmsPageCategoryId(self::PARENT_ID);
                }

                if ($query instanceof GetCmsPageCategoriesForBreadcrumb) {
                    return new Breadcrumb([]);
                }

                $this->fail('Unexpected query ' . get_class($query));
            }
        );

        return $queryBus;
    }
}
