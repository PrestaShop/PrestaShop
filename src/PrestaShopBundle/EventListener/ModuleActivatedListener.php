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

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeException;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShopBundle\Security\Annotation\ModuleActivated;
use ReflectionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Allow a redirection to the right url when using ModuleActivated annotation
 * and the module is inactive.
 */
class ModuleActivatedListener
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly TranslatorInterface $translator,
        private readonly Session $session,
        private readonly ModuleRepository $moduleRepository
    ) {
    }

    /**
     * @param ControllerEvent $event
     *
     * @throws AttributeException
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

        [$controllerObject, $methodName] = $controller;

        $method = new \ReflectionMethod($controllerObject, $methodName);
        $moduleActivated = array_merge(
            $method->getDeclaringClass()->getAttributes(ModuleActivated::class),
            $method->getAttributes(ModuleActivated::class)
        );

        if ([] === $moduleActivated) {
            return;
        }

        foreach ($moduleActivated as $moduleActivatedAttribute) {
            /** @var ModuleActivated $moduleActivated */
            $moduleActivatedAttribute = $moduleActivatedAttribute->newInstance();

            $this->validateAttribute($moduleActivatedAttribute, $controllerObject::class . '::' . $methodName);

            /** @var Module $module */
            $module = $this->moduleRepository->getModule($moduleActivatedAttribute->getModuleName());
            if (!$module->isActive()) {
                $this->showNotificationMessage($moduleActivatedAttribute);
                $url = $this->router->generate($moduleActivatedAttribute->getRedirectRoute());

                $event->setController(function () use ($url) {
                    return new RedirectResponse($url);
                });
                break;
            }
        }
    }

    /**
     * Send an error message when redirected, will only work on migrated pages.
     *
     * @param ModuleActivated $moduleActivated
     */
    private function showNotificationMessage(ModuleActivated $moduleActivated): void
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
     * @param ModuleActivated $attribute
     * @param string $attributePosition
     *
     * @throws AttributeException
     */
    private function validateAttribute(ModuleActivated $attribute, string $attributePosition): void
    {
        if (null === $attribute->getModuleName()) {
            throw new AttributeException(sprintf('You must specify @ModuleActivated(moduleName) annotation parameter on %s', $attributePosition));
        }

        if (null === $attribute->getRedirectRoute()) {
            throw new AttributeException(sprintf('You must specify @ModuleActivated(redirectRoute) annotation parameter on %s', $attributePosition));
        }
    }
}
