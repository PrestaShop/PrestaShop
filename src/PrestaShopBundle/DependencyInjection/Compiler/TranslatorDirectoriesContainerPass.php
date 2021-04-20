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

namespace PrestaShopBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

/**
 * The translator service loads translation files in the order specified in FrameworkExtension.
 * Unfortunately, the default directory (app/Resources/translations) is loaded in the end.
 * That made catalogue provided by the modules overridden by the one in the default directory.
 *
 * This compiler pass adds the modules' translation directories at the end of the stack.
 * With this, if provided, the translations of the modules will be the used ones.
 */
class TranslatorDirectoriesContainerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $translator = $container->findDefinition('translator');

        // Discover modules' translation directories
        $files = $translator->getArgument(4)['resource_files'];

        $dirs = [];

        $activeModules = $container->getParameter('kernel.active_modules');
        foreach ($activeModules as $activeModuleName) {
            $translationsDir = _PS_MODULE_DIR_ . $activeModuleName . '/translations';
            if (is_dir($translationsDir)) {
                $dirs[] = $translationsDir;
            }
        }

        // Register translation resources
        if ($dirs) {
            $finder = Finder::create()
                ->followLinks()
                ->files()
                ->filter(function (\SplFileInfo $file) {
                    return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
                })
                ->in($dirs)
                ->sortByName()
            ;

            foreach ($finder as $file) {
                list(, $locale) = explode('.', $file->getBasename(), 3);
                if (!isset($files[$locale])) {
                    $files[$locale] = [];
                }

                $files[$locale][] = (string) $file;
            }

            // Reinject resource_files parameter into the translator service
            $options = array_merge(
                $translator->getArgument(4),
                ['resource_files' => $files]
            );

            $translator->replaceArgument(4, $options);
        }
    }
}
