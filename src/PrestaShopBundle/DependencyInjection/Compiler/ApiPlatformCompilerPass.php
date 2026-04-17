<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Doctrine\Inflector\Inflector;
use PrestaShop\PrestaShop\Core\Util\Inflector as InflectorUtil;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiPlatformCompilerPass implements CompilerPassInterface
{
    private Inflector $inflector;

    public function __construct()
    {
        $this->inflector = InflectorUtil::getInflector();
    }

    public function process(ContainerBuilder $container): void
    {
        $scopes = [];
        if ($container->hasParameter('api_platform.oauth.scopes')) {
            $scopes = $container->getParameter('api_platform.oauth.scopes');
        }

        // The service is not accessible during early compiler phase so build it manually
        $apiResourceScopesExtractor = ApiResourceScopesExtractorFactory::build(
            $container,
            $container->getParameter('kernel.environment'),
            $container->getParameter('prestashop.module_dir'),
            $container->getParameter('prestashop.installed_modules'),
            $container->getParameter('prestashop.active_modules'),
            $container->getParameter('kernel.project_dir'),
        );
        foreach ($apiResourceScopesExtractor->getEnabledApiResourceScopes() as $apiResourceScope) {
            foreach ($apiResourceScope->getScopes() as $scope) {
                $scopes[$scope] = $this->humanizeScope($scope);
            }
        }

        // Update parameter of the APIPlatform framework so these scopes are displayed in the Swagger UI
        $container->setParameter('api_platform.oauth.scopes', $scopes);
    }

    private function humanizeScope(string $scope): string
    {
        $matches = [];
        if (preg_match('/(.*)_read/', $scope, $matches)) {
            if (!empty($matches[1])) {
                return 'Read ' . $this->inflector->capitalize($this->inflector->camelize($matches[1]));
            }
        }

        if (preg_match('/(.*)_write/', $scope, $matches)) {
            if (!empty($matches[1])) {
                return 'Write ' . $this->inflector->capitalize($this->inflector->camelize($matches[1]));
            }
        }

        return $scope;
    }
}
