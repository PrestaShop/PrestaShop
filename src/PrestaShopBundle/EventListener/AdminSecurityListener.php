<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
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
 */
class AdminSecurityListener
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authChecker)
    {
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

        $annotationReader = new AnnotationReader();
        $reflectionController = new ReflectionObject($controllerObject);

        $reflectionMethod = $reflectionController->getMethod($methodName);

        // attributes management
        $attributes = $reflectionMethod->getAttributes(AdminSecurity::class);

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $this->isGranted($attribute->newInstance(), $event->getRequest());
            }
        }

        // annotation management
        $annotation = $annotationReader->getMethodAnnotation($reflectionMethod, AdminSecurity::class);

        if ($annotation != null) {
            trigger_deprecation('prestashop/prestashop', '9.0', 'AdminSecurity annotation is deprecated, use attribute instead.');

            $this->isGranted($annotation, $event->getRequest());
        }
    }

    private function isGranted(AdminSecurity $adminSecurity, Request $request): void
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
