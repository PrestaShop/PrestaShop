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

namespace PrestaShopBundle;

use PrestaShopBundle\DependencyInjection\Compiler\CommandAndQueryCollectorPass;
use PrestaShopBundle\DependencyInjection\Compiler\CommandAndQueryRegisterPass;
use PrestaShopBundle\DependencyInjection\Compiler\DynamicRolePass;
use PrestaShopBundle\DependencyInjection\Compiler\GridDefinitionServiceIdsCollectorPass;
use PrestaShopBundle\DependencyInjection\Compiler\IdentifiableObjectFormTypesCollectorPass;
use PrestaShopBundle\DependencyInjection\Compiler\LoadServicesFromModulesPass;
use PrestaShopBundle\DependencyInjection\Compiler\ModuleControllerRegisterPass;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\OptionsFormHookNameCollectorPass;
use PrestaShopBundle\DependencyInjection\Compiler\OverrideTranslatorServiceCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\PopulateTranslationProvidersPass;
use PrestaShopBundle\DependencyInjection\Compiler\RemoveXmlCompiledContainerPass;
use PrestaShopBundle\DependencyInjection\Compiler\RouterPass;
use PrestaShopBundle\DependencyInjection\Compiler\TestEnvironmentPass;
use PrestaShopBundle\DependencyInjection\PrestaShopExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\ResolveClassPass;
use Symfony\Component\DependencyInjection\Compiler\ResolveInstanceofConditionalsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PrestaShopBundle extends Bundle
{
    /**
     * The priority of @see LoadServicesFromModulesPass should be higher
     * than the Symfony's @see ResolveClassPass
     * and @see ResolveInstanceofConditionalsPass
     *
     * @see PassConfig::__construct
     * @see https://github.com/PrestaShop/PrestaShop/pull/30588 for details
     */
    public const LOAD_MODULE_SERVICES_PASS_PRIORITY = 200;

    public function __construct(private \AppKernel $kernel)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PrestaShopExtension();
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DynamicRolePass());
        $container->addCompilerPass(new PopulateTranslationProvidersPass());
        $container->addCompilerPass(new LoadServicesFromModulesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, self::LOAD_MODULE_SERVICES_PASS_PRIORITY);
        $container->addCompilerPass(new LoadServicesFromModulesPass($this->kernel->getAppType()), PassConfig::TYPE_BEFORE_OPTIMIZATION, self::LOAD_MODULE_SERVICES_PASS_PRIORITY);
        $container->addCompilerPass(new ModuleControllerRegisterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, self::LOAD_MODULE_SERVICES_PASS_PRIORITY);
        $container->addCompilerPass(new RemoveXmlCompiledContainerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new RouterPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new OverrideTranslatorServiceCompilerPass());
        $container->addCompilerPass(new ModulesDoctrineCompilerPass());
        $container->addCompilerPass(new CommandAndQueryRegisterPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, self::LOAD_MODULE_SERVICES_PASS_PRIORITY);
        $container->addCompilerPass(new CommandAndQueryCollectorPass());
        $container->addCompilerPass(new OptionsFormHookNameCollectorPass());
        $container->addCompilerPass(new GridDefinitionServiceIdsCollectorPass());
        $container->addCompilerPass(new IdentifiableObjectFormTypesCollectorPass());
        $container->addCompilerPass(new TestEnvironmentPass());
    }
}
