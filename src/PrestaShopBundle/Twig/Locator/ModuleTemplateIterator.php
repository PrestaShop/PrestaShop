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

namespace PrestaShopBundle\Twig\Locator;

use Symfony\Bundle\TwigBundle\TemplateIterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Extended class: Iterator for all templates in bundles and in the application Resources directory.
 * This one applies a specific filter on the module folder, in order to take only *.twig
 */
class ModuleTemplateIterator extends TemplateIterator
{
    private $kernel;
    private $rootDir;
    private $templates;
    private $paths;
    private $defaultPath;

    /**
     * @param KernelInterface $kernel A KernelInterface instance
     * @param string $rootDir The directory where global templates can be stored
     * @param array $paths Additional Twig paths to warm
     * @param string $defaultPath The directory where global templates can be stored
     */
    public function __construct(KernelInterface $kernel, $rootDir, array $paths = array(), $defaultPath = null)
    {
        $this->paths = [];
        $this->kernel = $kernel;
        $this->rootDir = $rootDir;
        $this->defaultPath = $defaultPath;

        $keyToRemove = array_search('Modules', $paths);
        // If the key was found, move it in a new array
        if (false !== $keyToRemove) {
            $exceptionPath = $paths[$keyToRemove];
            unset($paths[$keyToRemove]);
            $this->paths = [$keyToRemove => $exceptionPath];
        }
        parent::__construct($kernel, $rootDir, $paths, $defaultPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if (null !== $this->templates) {
            return $this->templates;
        }

        // Not done yet, we need the array back
        $this->templates = iterator_to_array(parent::getIterator());

        foreach ($this->paths as $dir => $namespace) {
            $this->templates = array_merge($this->templates, $this->findTemplatesInDirectory($dir, $namespace));
        }

        return $this->templates = new \ArrayIterator(array_unique($this->templates));
    }

    /**
     * Find templates in the given directory.
     *
     * @param string $dir The directory where to look for templates
     * @param string|null $namespace The template namespace
     *
     * @return array
     */
    private function findTemplatesInDirectory($dir, $namespace = null, array $excludeDirs = array())
    {
        if (!is_dir($dir)) {
            return array();
        }

        $templates = array();
        foreach (Finder::create()->files()->name('*.twig')->followLinks()->in($dir)->exclude($excludeDirs) as $file) {
            $templates[] = (null !== $namespace ? '@' . $namespace . '/' : '') . str_replace('\\', '/', $file->getRelativePathname());
        }

        return $templates;
    }
}
