<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            throw new ServiceDefinitionException('The security.access.role_hierarchy_voter service is already defined', 'security.access.role_hierarchy_voter');
        }

        $roleHierarchyVoterDefinition = $container->register(
            'security.access.role_hierarchy_voter',
            Voter::class
        );

        $roleHierarchyVoterDefinition
            ->setPublic(false)
            ->addArgument(new Reference('prestashop.security.role.dynamic_role_hierarchy'))
            ->addTag('security.voter', ['priority' => 245]);
    }
}
