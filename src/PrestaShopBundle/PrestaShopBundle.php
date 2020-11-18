<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle;

use PrestaShopBundle\DependencyInjection\Compiler\DynamicRolePass;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\LoadServicesFromModulesPass;
use PrestaShopBundle\DependencyInjection\Compiler\OverrideTranslatorServiceCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\OverrideTwigServiceCompilerPass;
use PrestaShopBundle\DependencyInjection\Compiler\PopulateTranslationProvidersPass;
use PrestaShopBundle\DependencyInjection\Compiler\RemoveXmlCompiledContainerPass;
use PrestaShopBundle\DependencyInjection\Compiler\RouterPass;
use PrestaShopBundle\DependencyInjection\PrestaShopExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PrestaShopBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
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
        $container->addCompilerPass(new LoadServicesFromModulesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new LoadServicesFromModulesPass('admin'), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $container->addCompilerPass(new RemoveXmlCompiledContainerPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new RouterPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new OverrideTranslatorServiceCompilerPass());
        $container->addCompilerPass(new OverrideTwigServiceCompilerPass());
        $container->addCompilerPass(new ModulesDoctrineCompilerPass());
    }
}
