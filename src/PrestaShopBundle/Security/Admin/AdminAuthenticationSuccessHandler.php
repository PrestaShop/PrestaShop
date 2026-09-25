<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
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
        private readonly EmployeeRepository $employeeRepository,
        private readonly RouterInterface $router,
    ) {
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        if ($token instanceof TwoFactorTokenInterface) {
            return new RedirectResponse($this->router->generate('2fa_login'));
        }

        $user = $this->employeeRepository->loadEmployeeByIdentifier($token->getUserIdentifier(), true);
        if ($user instanceof Employee && $this->requiresTwoFactorSetup($user)) {
            return new RedirectResponse($this->router->generate('admin_employees_edit', [
                'employeeId' => $user->getId(),
            ]));
        }

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

    private function requiresTwoFactorSetup(Employee $employee): bool
    {
        if (!$employee->isTwoFactorRequired()) {
            return false;
        }

        if (!$employee->getTwoFactorEnabled()) {
            return true;
        }

        return !$employee->isEmailAuthEnabled() && !$employee->isTotpAuthenticationEnabled();
    }
}
