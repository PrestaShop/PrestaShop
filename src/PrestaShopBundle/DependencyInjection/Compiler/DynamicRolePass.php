<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShopBundle\Exception\ServiceDefinitionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Sets dynamic role hierarchy in the voter.
 */
class DynamicRolePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /*
         * @see Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension:createRoleHierarchy
         */
        if ($container->hasDefinition('security.access.role_hierarchy_voter')) {
            throw new ServiceDefinitionException(
                'The security.access.role_hierarchy_voter service is already defined',
                'security.access.role_hierarchy_voter'
            );
        }

        $roleHierarchyVoterDefinition = $container->register(
            'security.access.role_hierarchy_voter',
            Voter::class
        );

        $roleHierarchyVoterDefinition
            ->setPublic(false)
            ->addArgument(new Reference('prestashop.security.role.dynamic_role_hierarchy'))
            ->addTag('security.voter', array('priority' => 245));
    }
}
