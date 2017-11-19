<?php

/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Twig\Locator;

use Twig\Loader\FilesystemLoader;

/**
 * Loads templates from PrestaShop modules.
 */
class ModuleTemplateLoader extends FilesystemLoader
{
    /**
     * {@inheritdoc}
     */
    private $rootPath;

    /**
     * @param string|array $paths    A path or an array of paths where to look for templates
     * @param string|null  $rootPath The root path common to all relative paths (null for getcwd())
     * @param string|null  $namespace A path namespace
     */
    public function __construct($paths = array(), $rootPath = null, $namespace = 'PrestaShop')
    {
        $this->rootPath = (null === $rootPath ? getcwd() : $rootPath).DIRECTORY_SEPARATOR;
        if (false !== $realPath = realpath($rootPath)) {
            $this->rootPath = $realPath.DIRECTORY_SEPARATOR;
        }

        if ($paths) {
            $templatePaths = array();

            foreach ($paths as $path) {
                if (is_dir($dir = $path . '/views/'. $namespace)) {
                    $templatePaths[] = $dir;
                }
            }
            $this->setPaths($templatePaths, $namespace);
        }
    }
}
