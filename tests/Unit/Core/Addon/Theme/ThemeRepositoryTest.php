<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Core\Addon;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Phake;

class ThemeRepositoryTest extends TestCase
{
    const NOTICE = '[ThemeRepository] ';
    private $repository;

    protected function setUp()
    {
        $shop = Phake::mock('Shop');
        $shop->id = 1;
        $shop->name = 'Demo shop';

        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($shop);

        /* @var \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository */
        $this->repository = new ThemeRepository(
            $configuration,
            new Filesystem(),
            $shop
        );
    }

    protected function tearDown()
    {
        $this->repository = null;
    }

    public function testGetInstanceByName()
    {
        $expectedTheme = $this->repository->getInstanceByName('classic');
        $this->assertInstanceOf('PrestaShop\PrestaShop\Core\Addon\Theme\Theme',
            $expectedTheme,
            self::NOTICE.sprintf('expected `getInstanceByName to return Theme, get %s`', gettype($expectedTheme))
        );
    }

    public function testGetInstanceByNameNotFound()
    {
        $this->setExpectedException('PrestaShopException');
        $this->repository->getInstanceByName('not_found');
    }

    public function testGetList()
    {
        $themeList = $this->repository->getList();
        $this->assertInternalType('array', $themeList);
        $this->assertInstanceOf('PrestaShop\PrestaShop\Core\Addon\Theme\Theme', current($themeList));
    }

    public function testGetListExcluding()
    {
        $themeListWithoutRestrictions = $this->repository->GetListExcluding([]);
        $themeListWithoutClassic = $this->repository->GetListExcluding(['classic']);
        $this->assertEquals($themeListWithoutRestrictions,
            $this->repository->getList(),
            self::NOTICE.sprintf('expected list excluding without args to return complete list of themes `see ThemeRepository::getListExcluding`')
        );

        $this->assertCount((count($themeListWithoutRestrictions) - 1),
            $themeListWithoutClassic,
            self::NOTICE.sprintf('expected list excluding with classic to list of themes without classic')
        );
    }
}
