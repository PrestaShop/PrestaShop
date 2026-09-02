<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Routing\Linter;

use PrestaShopBundle\Routing\Linter\Exception\LinterException;
use PrestaShopBundle\Routing\Linter\SecurityAttributeLinter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Route;
use Tests\Resources\Controller\TestController;

class SecurityAttributeLinterTest extends KernelTestCase
{
    /**
     * @var SecurityAttributeLinter
     */
    private $securityAnnotationLinter;

    protected function setUp(): void
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
}
