<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Alias Custom Security Router to Symfony framework's one.
 *
 * Allows the CSRF Token in URL strategy
 *
 * @see https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet#Disclosure_of_Token_in_URL
 */
class RouterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Only the Admin app defines prestashop.router, since the CSRF token in the URL is part of
        // the back office security model. The front office and the Admin API keep Symfony's router,
        // so the URLs they generate - an API Location header, for instance - carry no admin token.
        if (!$container->hasDefinition('prestashop.router')) {
            return;
        }

        $container->setAlias('router', 'prestashop.router')->setPublic(true);
    }
}
