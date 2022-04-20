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

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use ReflectionClass;
use ReflectionObject;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Allow a redirection to the right url when using ModuleActivated annotation
 * and the module is inactive.
 */
class ModuleActivatedListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param Session $session
     * @param Reader $annotationReader
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(
        RouterInterface $router,
        TranslatorInterface $translator,
        Session $session,
        Reader $annotationReader,
        ModuleRepository $moduleRepository
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->session = $session;
        $this->annotationReader = $annotationReader;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        list($controllerObject, $methodName) = $controller;
        $moduleActivated = $this->getAnnotation($controllerObject, $methodName);

        if (null === $moduleActivated) {
            return;
        }

        /** @var Module $module */
        $module = $this->moduleRepository->getModule($moduleActivated->getModuleName());
        if (!$module->isActive()) {
            $this->showNotificationMessage($moduleActivated);
            $url = $this->router->generate($moduleActivated->getRedirectRoute());

            $event->setController(function () use ($url) {
                return new RedirectResponse($url);
            });
        }
    }

    /**
     * Send an error message when redirected, will only work on migrated pages.
     *
     * @param ModuleActivated $moduleActivated
     */
    private function showNotificationMessage(ModuleActivated $moduleActivated)
    {
        $this->session->getFlashBag()->add(
            'error',
            $this->translator->trans(
                $moduleActivated->getMessage(),
                [$moduleActivated->getModuleName()],
                $moduleActivated->getDomain()
            )
        );
    }

    /**
     * @param object $controllerObject
     * @param string $methodName
     *
     * @return ModuleActivated|null
     *
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    private function getAnnotation($controllerObject, $methodName)
    {
        $tokenAnnotation = ModuleActivated::class;

        $controllerClass = ClassUtils::getClass($controllerObject);
        $classAnnotation = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($controllerClass),
            $tokenAnnotation
        );

        if (null !== $classAnnotation && $classAnnotation instanceof ModuleActivated) {
            $this->validateAnnotation($classAnnotation, $controllerClass);

            return $classAnnotation;
        }

        $controllerReflectionObject = new ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);

        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, $tokenAnnotation);

        if (null !== $annotation && $annotation instanceof ModuleActivated) {
            $this->validateAnnotation($annotation, $controllerClass . '::' . $methodName);

            return $annotation;
        }

        return null;
    }

    /**
     * @param ModuleActivated $annotation
     * @param string $annotationPosition
     *
     * @throws AnnotationException
     */
    private function validateAnnotation(ModuleActivated $annotation, $annotationPosition)
    {
        if (null === $annotation->getModuleName()) {
            throw new AnnotationException(sprintf('You must specify @ModuleActivated(moduleName) annotation parameter on %s', $annotationPosition));
        }

        if (null === $annotation->getRedirectRoute()) {
            throw new AnnotationException(sprintf('You must specify @ModuleActivated(redirectRoute) annotation parameter on %s', $annotationPosition));
        }
    }
}
