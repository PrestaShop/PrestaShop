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

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryContainerInterface;
use PrestaShopBundle\Grid\GridFactoryContainer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * Alias Custom Security Router to Symfony framework's one.
 *
 * Allows the CSRF Token in URL strategy
 *
 * @see https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet#Disclosure_of_Token_in_URL
 */
class GridFactoryPass implements CompilerPassInterface
{
    private const TAG = 'prestashop.grid_factory';

    public function process(ContainerBuilder $container): void
    {
        $definitions = $container->findTaggedServiceIds(self::TAG);

        $refMap = [];

        foreach ($definitions as $serviceId => $tagData) {
            if (count($tagData) > 1) {
                throw new \InvalidArgumentException(sprintf('Cannot use many "%s" tags on %s.', self::TAG, $serviceId));
            }

            $gridName = $tagData[0]['grid'];

            if (array_key_exists($gridName, $refMap)) {
                throw new \InvalidArgumentException(sprintf('There is already a grid with the name "%s"', $serviceId));
            }

            $refMap[$gridName] = new Reference($serviceId);
        }

        $locator = (new Definition(ServiceLocator::class))
            ->addArgument($refMap)
            ->setPublic(false)
            ->addTag('container.service_locator');

        $gridManagerDefinition = new Definition(GridFactoryContainer::class);
        $gridManagerDefinition->setAutowired(true);
        $gridManagerDefinition->addArgument($locator);
        $container->setDefinition(GridFactoryContainerInterface::class, $gridManagerDefinition);
    }
}
