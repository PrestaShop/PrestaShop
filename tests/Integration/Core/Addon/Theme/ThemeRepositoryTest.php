<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Addon\Theme;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Tests\TestCase\ContextStateTestCase;

class ThemeRepositoryTest extends ContextStateTestCase
{
    public const NOTICE = '[ThemeRepository] ';
    /**
     * @var ThemeRepository|null
     */
    private $repository;

    protected function setUp(): void
    {
        $context = $this->createContextMock([
            'shop' => $this->createContextFieldMock(Shop::class, 1),
        ]);
        Shop::setContext(Shop::CONTEXT_SHOP, 1);

        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($context->shop);

        $this->repository = new ThemeRepository(
            $configuration,
            new Filesystem(),
            $context->shop
        );
    }

    protected function tearDown(): void
    {
        $this->repository = null;
    }

    public function testGetInstanceByName()
    {
        $expectedTheme = $this->repository->getInstanceByName(Theme::getDefaultTheme());
        $this->assertInstanceOf(
            'PrestaShop\PrestaShop\Core\Addon\Theme\Theme',
            $expectedTheme,
            self::NOTICE . sprintf('expected `getInstanceByName to return Theme, get %s`', gettype($expectedTheme))
        );
    }

    public function testGetInstanceByNameNotFound()
    {
        $this->expectException('PrestaShopException');
        $this->repository->getInstanceByName('not_found');
    }

    public function testGetList()
    {
        $themeList = $this->repository->getList();
        $this->assertIsArray($themeList);
        $this->assertInstanceOf('PrestaShop\PrestaShop\Core\Addon\Theme\Theme', current($themeList));
    }

    public function testGetListExcluding()
    {
        $themeListWithoutRestrictions = $this->repository->GetListExcluding([]);
        $themeListWithoutClassic = $this->repository->GetListExcluding([Theme::getDefaultTheme()]);
        $this->assertEquals(
            $themeListWithoutRestrictions,
            $this->repository->getList(),
            self::NOTICE . sprintf('expected list excluding without args to return complete list of themes `see ThemeRepository::getListExcluding`')
        );

        $this->assertCount(
            count($themeListWithoutRestrictions) - 1,
            $themeListWithoutClassic,
            self::NOTICE . sprintf('expected list excluding with classic to list of themes without classic')
        );
    }
}
