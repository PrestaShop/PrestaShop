<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Resources\Controller;

use PrestaShopBundle\Security\Admin\RequestAttributes;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class is used to test #[AdminSecurity()] attribute linter
 */
#[Route('/tests')]
class TestController extends AbstractController
{
    #[AdminSecurity(new Expression('is_granted("ROLE_UNKNOWN")'))]
    #[Route('/', name: 'test_index')]
    public function indexAction()
    {
        return new Response();
    }

    #[Route('/create', name: 'test_create')]
    public function createAction()
    {
        return new Response();
    }

    #[AdminSecurity(new Expression('is_granted("ROLE_EMPLOYEE")'))]
    #[Route('/something-complex', name: 'test_something_complex')]
    public function doSomethingComplexAction()
    {
        return new Response('ComplexAction');
    }

    #[AdminSecurity(new Expression('is_granted("ROLE_UNKNOWN")'), redirectRoute: 'test_something_complex')]
    #[Route('/test-redirect', name: 'test_redirect')]
    public function doRedirectIfForbidden(): Response
    {
        return new Response();
    }

    #[Route('/anonymous-controller', name: 'test_anonymous', defaults: [RequestAttributes::ANONYMOUS_CONTROLLER_ATTRIBUTE => true])]
    public function anonymousController()
    {
        return new Response('AnonymousController');
    }

    #[Route('/anonymous-hard-coded-controller', name: 'test_hard_coded_anonymous', defaults: ['_anonymous_controller' => true])]
    public function hardCodedAnonymousController()
    {
        return new Response('AnonymousController');
    }
}
