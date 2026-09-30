<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Group\GroupDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\CategoryProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CategoryFormDataProvider;
use Symfony\Component\Routing\Router;

class CategoryFormDataProviderTest extends TestCase
{
    public function testDefaultDataUsesGroupsFilteredByCurrentShopContext(): void
    {
        $groupIds = [1, 2, 3];
        $categoryId = 2;
        $associatedShopIds = [1];
        $seoPreviewUrl = 'https://example.test/category/{friendly-url}';

        $queryBus = $this->createMock(CommandBusInterface::class);

        $groupDataProvider = $this->createMock(GroupDataProvider::class);
        $groupDataProvider
            ->expects($this->once())
            ->method('getAllGroupIds')
            ->with(true)
            ->willReturn($groupIds);

        $link = $this->createMock(Link::class);
        $link
            ->expects($this->once())
            ->method('getCategoryLink')
            ->with(0, '{friendly-url}')
            ->willReturn($seoPreviewUrl);

        $categoryProvider = new CategoryProvider($link);

        $router = $this->createMock(Router::class);

        $shopContext = $this->createMock(ShopContext::class);
        $shopContext
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);
        $shopContext
            ->expects($this->once())
            ->method('getAssociatedShopIds')
            ->willReturn($associatedShopIds);

        $dataProvider = new CategoryFormDataProvider(
            $queryBus,
            $groupDataProvider,
            $categoryProvider,
            $router,
            $shopContext
        );

        $this->assertSame(
            [
                'id_parent' => $categoryId,
                'group_association' => $groupIds,
                'shop_association' => $associatedShopIds,
                'active' => true,
                'seo_preview' => $seoPreviewUrl,
            ],
            $dataProvider->getDefaultData()
        );
    }
}
