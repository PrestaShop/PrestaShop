<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

use PrestaShopBundle\Entity\Employee\Employee;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * This handle is called when the employee successfully logs in to the back office, its purpose is
 * to dynamically set the route to redirect to based on the Employee's configured homepage.
 */
class AdminAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    use TargetPathTrait;

    public function __construct(
        private readonly EmployeeHomepageProvider $employeeHomepageProvider,
        private readonly RouterInterface $router,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        if ($request->hasPreviousSession()) {
            $redirectUrl = $this->getTargetPath($request->getSession(), 'main');
        }
        if (empty($redirectUrl)) {
            $redirectUrl = $this->employeeHomepageProvider->getHomepageUrl();
        }
        if (empty($redirectUrl)) {
            $redirectUrl = $this->router->generate('admin_homepage');
        }

        return new RedirectResponse($redirectUrl);
    }
}
