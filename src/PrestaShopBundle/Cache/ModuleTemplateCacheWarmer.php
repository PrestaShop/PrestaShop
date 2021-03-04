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

namespace PrestaShopBundle\Cache;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinderInterface;
use Symfony\Bundle\TwigBundle\CacheWarmer\TemplateCacheCacheWarmer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Templating\TemplateReference;
use Twig\Error\Error;

/**
 * Generates the Twig cache for all paths, with a specific filter for given namespace
 */
class ModuleTemplateCacheWarmer extends TemplateCacheCacheWarmer
{
    private $paths;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContainerInterface $container, TemplateFinderInterface $finder = null, $paths = [])
    {
        $this->paths = [];
        $keyToRemove = array_search('Modules', $paths);
        // If the key was found, move it in a new array
        if (false !== $keyToRemove) {
            $exceptionPath = $paths[$keyToRemove];
            unset($paths[$keyToRemove]);
            $this->paths = [$keyToRemove => $exceptionPath];
        }
        parent::__construct($container, $finder, $paths);
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        // Default behavior for all folder except Modules
        parent::warmUp($cacheDir);

        $twig = $this->container->get('twig');
        $templates = [];

        foreach ($this->paths as $path => $namespace) {
            $templates = array_merge($templates, $this->findTemplatesInFolder($namespace, $path));
        }

        foreach ($templates as $template) {
            if ('twig' !== $template->get('engine')) {
                continue;
            }

            try {
                $twig->loadTemplate($template);
            } catch (Error $e) {
                // problem during compilation, give up
            }
        }
    }

    /**
     * Find templates from *.twig files in the given directory.
     *
     * @param string $namespace The namespace for these templates
     * @param string $dir The folder where to look for templates
     *
     * @return array An array of templates of type TemplateReferenceInterface
     */
    private function findTemplatesInFolder($namespace, $dir)
    {
        if (!is_dir($dir)) {
            return [];
        }

        $templates = [];
        $finder = new Finder();

        foreach ($finder->files()->followLinks()->name('*.twig')->in($dir) as $file) {
            $name = $file->getRelativePathname();
            $templates[] = new TemplateReference(
                $namespace ? sprintf('@%s/%s', $namespace, $name) : $name,
                'twig'
            );
        }

        return $templates;
    }
}
