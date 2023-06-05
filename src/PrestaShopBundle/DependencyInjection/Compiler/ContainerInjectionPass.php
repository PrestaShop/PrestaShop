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

declare(strict_types=1);

namespace PrestaShopBundle\DependencyInjection\Compiler;

use InvalidArgumentException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * As explained in https://github.com/symfony/symfony/issues/36567,
 * Controllers lose the ControllerAwareTrait capabilities when they are decorated.
 *
 * This pass injects the container into PrestaShop tagged controllers to overcome this issue.
 *
 * @deprecated since 9.0, to be removed in 10.0. Controller are now services and can use dependency injection.
 */
class ContainerInjectionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $controllers = $container->findTaggedServiceIds(FrameworkBundleAdminController::PRESTASHOP_CORE_CONTROLLERS_TAG);

        foreach ($controllers as $id => $controller) {
            $definition = $container->findDefinition($id);
            $class = $definition->getClass();
            $reflectedClass = $container->getReflectionClass($class);

            if (null === $reflectedClass) {
                throw new InvalidArgumentException(sprintf(
                    'Class "%s" used for service "%s" cannot be found.',
                    $class,
                    $id
                ));
            }

            $isContainerAware = (
                $reflectedClass->implementsInterface(ContainerAwareInterface::class)
                || is_subclass_of($class, FrameworkBundleAdminController::class)
            );

            if ($isContainerAware) {
                $definition->addMethodCall('setContainer', [new Reference('service_container')]);
            }
        }
    }
}
