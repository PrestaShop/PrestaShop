<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Routing\Linter;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use PrestaShopBundle\Routing\Linter\SecurityAnnotationLinter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Route;
use Tests\Resources\Controller\TestController;

class SecurityAnnotationLinterTest extends KernelTestCase
{
    /**
     * @var SecurityAnnotationLinter
     */
    private $securityAnnotationLinter;

    protected function setUp()
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->securityAnnotationLinter = $container->get('prestashop.bundle.routing.linter.security_annotation_linter');
    }

    public function testLinterPassesWhenRouteControllerHasConfiguredAdminSecurityAnnotation()
    {
        $route = new Route('/', [
            '_controller' => sprintf('%s::%s', TestController::class, 'indexAction'),
        ]);

        $this->securityAnnotationLinter->lint('route_name', $route);

        $this->assertTrue($exceptionWasNotThrown = true);
    }

    public function testLinterThrowsExceptionWhenRouteControllerDoesNotHaveConfiguredAdminSecutityAnnotation()
    {
        $route = new Route('/', [
            '_controller' => sprintf('%s::%s', TestController::class, 'createAction'),
        ]);

        $this->expectException(LinterException::class);

        $this->securityAnnotationLinter->lint('route_name', $route);
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
    }
}
