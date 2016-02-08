<?php
/**
 * 2007-2015 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Tests\Core\Addon;

use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Phake;
use Shop;

class ThemeRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $repository;

    protected function setUp()
    {
        $shop = Phake::mock('Shop');
        $shop->id = 1;
        $shop->name = 'Demo shop';

        /* @var \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository */
        $this->repository = new ThemeRepository(
            new Configuration($shop),
            $shop
        );
    }

    protected function tearDown()
    {
        $this->repository = null;
    }

    public function testGetInstanceByName()
    {
    }

    public function testGetList()
    {
    }

    public function testGetListExcluding()
    {
    }

    public function testGetFilteredList()
    {
    }
}
