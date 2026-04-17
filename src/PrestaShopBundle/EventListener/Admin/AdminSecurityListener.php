<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\EventListener\Admin;

use Doctrine\Common\Annotations\Reader;
use PrestaShopBundle\Security\Annotation\AdminSecurity as AdminSecurityAnnotation;
use PrestaShopBundle\Security\Attribute\AdminSecurity as AdminSecurityAttribute;
use ReflectionException;
use ReflectionObject;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Security layer for annotations and AdminSecurity attributes.
 * It is based on Symfony's IsGrantedListener, we have adapted it for the use of our own AdminSecurity attribute,
 * itself based on IsGranted attribute
 */
class AdminSecurityListener
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly Reader $annotationReader,
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        /** @var object $controllerObject */
        [$controllerObject, $methodName] = $controller;
        $reflectionController = new ReflectionObject($controllerObject);

        $reflectionMethod = $reflectionController->getMethod($methodName);

        // attributes management
        $classAttributes = $reflectionController->getAttributes(AdminSecurityAttribute::class);
        $methodAttributes = $reflectionMethod->getAttributes(AdminSecurityAttribute::class);
        $attributes = array_merge($classAttributes, $methodAttributes);

        foreach ($attributes as $attribute) {
            $this->isGranted($attribute->newInstance(), $event->getRequest());
        }

        // annotation management
        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, AdminSecurityAnnotation::class);

        if ($annotation != null) {
            trigger_deprecation('prestashop/prestashop', '9.0', 'AdminSecurity annotation is deprecated, use attribute instead.');

            $this->isGranted($annotation, $event->getRequest());
        }
    }

    private function isGranted(AdminSecurityAttribute $adminSecurity, Request $request): void
    {
        $attribute = $adminSecurity->getAttribute();

        if (!$attribute instanceof Expression) {
            $attribute = new Expression($attribute);
        }

        if (!$this->authChecker->isGranted($attribute, $request)) {
            $message = $adminSecurity->getMessage();

            if ($statusCode = $adminSecurity->getStatusCode()) {
                throw new HttpException($statusCode, $message, code: $attribute->exceptionCode ?? 0);
            }

            $accessDeniedException = new AccessDeniedException($message, code: $attribute->exceptionCode ?? 403);
            $accessDeniedException->setAttributes($adminSecurity->getAttribute());

            throw $accessDeniedException;
        }
    }
}
