<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Locator;

use Twig\Loader\FilesystemLoader;

/**
 * Loads templates from PrestaShop modules.
 */
class ModuleTemplateLoader extends FilesystemLoader
{
    /**
     * @param array $namespaces a collection of path namespaces with namespace names
     * @param array $modulePaths A path or an array of paths where to look for module templates
     */
    public function __construct(array $namespaces, array $modulePaths = [])
    {
        if (!empty($modulePaths)) {
            $this->registerNamespacesFromConfig($modulePaths, $namespaces);
        }
    }

    /**
     * Register namespaces in module and link them to the right paths.
     *
     * @param array $modulePaths
     * @param array $namespaces
     */
    private function registerNamespacesFromConfig(array $modulePaths, array $namespaces)
    {
        foreach ($namespaces as $namespace => $namespacePath) {
            $templatePaths = [];

            foreach ($modulePaths as $path) {
                if (is_dir($dir = $path . '/views/PrestaShop/' . $namespacePath)) {
                    $templatePaths[] = $dir;
                }
            }
            $this->setPaths($templatePaths, $namespace);
        }
    }
}
