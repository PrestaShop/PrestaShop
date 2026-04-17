<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Used to configure services specifically for the test environment.
 */
class TestEnvironmentPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $env = $container->getParameter('kernel.environment');

        if ('test' === $env) {
            // see https://symfony.com/doc/current/testing.html#multiple-requests-in-one-test
            $container->getDefinition('security.token_storage')->clearTag('kernel.reset');
            $container->getDefinition('doctrine')->clearTag('kernel.reset');
        }
    }
}
