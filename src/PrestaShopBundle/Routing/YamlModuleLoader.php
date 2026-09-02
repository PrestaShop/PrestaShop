<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing;

use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * This class is responsible of loading routes of enabled modules.
 */
class YamlModuleLoader extends Loader
{
    /**
     * @var array the list of activated modules
     */
    private $installedModulesPaths;

    /**
     * @var bool we load the route collection only once per request
     */
    private $isLoaded = false;

    public function __construct(array $installedModulesPaths)
    {
        $this->installedModulesPaths = $installedModulesPaths;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->isLoaded) {
            throw new RuntimeException('Do not add the "module" loader twice.');
        }

        $routes = new RouteCollection();

        foreach ($this->installedModulesPaths as $modulePath) {
            $routingFile = $modulePath . '/config/routes.yml';
            if (file_exists($routingFile)) {
                $loadedRoutes = $this->import($routingFile, 'yaml');
                $routes->addCollection($loadedRoutes);
            }
        }

        $this->isLoaded = true;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'module' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function import($resource, $type = null)
    {
        $loadedRoutes = parent::import($resource, $type);

        return $this->modifyRoutes($loadedRoutes);
    }

    /**
     * @param RouteCollection $routes
     *
     * @return RouteCollection
     */
    private function modifyRoutes(RouteCollection $routes)
    {
        foreach ($routes->getIterator() as $route) {
            if ($route->hasDefault('_disable_module_prefix') && $route->getDefault('_disable_module_prefix') === true) {
                continue;
            }

            $route->setPath('/modules' . $route->getPath());
        }

        return $routes;
    }
}
