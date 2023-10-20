<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Addon\Theme;

use PrestaShop\PrestaShop\Adapter\Configuration;
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
        $expectedTheme = $this->repository->getInstanceByName('classic');
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
        $themeListWithoutClassic = $this->repository->GetListExcluding(['classic']);
        $this->assertEquals(
            $themeListWithoutRestrictions,
            $this->repository->getList(),
            self::NOTICE . sprintf('expected list excluding without args to return complete list of themes `see ThemeRepository::getListExcluding`')
        );

        $this->assertCount(
            (count($themeListWithoutRestrictions) - 1),
            $themeListWithoutClassic,
            self::NOTICE . sprintf('expected list excluding with classic to list of themes without classic')
        );
    }
}
