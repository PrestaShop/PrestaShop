<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class RequiredTwoFactorSetupListener
{
    use TargetPathTrait;

    private const ALLOWED_ROUTES = [
        'admin_employees_edit',
        'admin_employees_change_form_language',
        'admin_employees_toggle_navigation',
        'admin_logout',
    ];

    public function __construct(
        private readonly Security $security,
        private readonly EmployeeRepository $employeeRepository,
        private readonly RouterInterface $router,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->security->getToken();
        if ($token instanceof TwoFactorTokenInterface) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof Employee) {
            return;
        }

        $employee = $this->employeeRepository->loadEmployeeByIdentifier($user->getUserIdentifier(), true);
        if (!$employee instanceof Employee || !$this->requiresTwoFactorSetup($employee)) {
            return;
        }

        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route');
        if ($this->isAllowedRoute($route, $employee, $request)) {
            return;
        }

        if ($request->hasSession() && $request->isMethodSafe() && !$request->isXmlHttpRequest()) {
            $this->saveTargetPath($request->getSession(), 'main', $request->getUri());
        }

        $event->setResponse(new RedirectResponse($this->router->generate('admin_employees_edit', [
            'employeeId' => $employee->getId(),
        ])));
        $event->stopPropagation();
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

    private function isAllowedRoute(string $route, Employee $employee, \Symfony\Component\HttpFoundation\Request $request): bool
    {
        if (!in_array($route, self::ALLOWED_ROUTES, true)) {
            return false;
        }

        if ('admin_employees_edit' !== $route) {
            return true;
        }

        return (int) $request->attributes->get('employeeId') === $employee->getId();
    }
}
