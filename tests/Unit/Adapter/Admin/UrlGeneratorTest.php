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
namespace Tests\Unit\Adapter\Admin;

use PrestaShop\PrestaShop\Adapter\Admin\UrlGenerator;
use Tests\TestCase\UnitTestCase;
use Phake;

class UrlGeneratorTest extends UnitTestCase
{
    private $legacyContext;

    public function setUp()
    {
        parent::setUp();

        $this->context->language = new \Language;
        $this->context->language->id = 42;
        $this->legacyContext = Phake::partialMock('PrestaShop\\PrestaShop\\Adapter\\LegacyContext');
        Phake::when($this->legacyContext)->getAdminBaseUrl()->thenReturn('admin_fake_base');

        $this->setupSfKernel();
    }

    public function test_generate_equivalent_route()
    {
        $router = $this->sfKernel->getContainer()->get('router');
        $generator = new UrlGenerator($this->legacyContext, $router);

        // the following route contains a "_legacy" equivalent
        list($controller, $parameters) = $generator->getLegacyOptions('admin_product_catalog');
        $this->assertEquals('AdminProducts', $controller);
        $this->assertCount(0, $parameters);
    }
}
