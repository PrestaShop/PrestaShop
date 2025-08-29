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

namespace PrestaShopBundle\DependencyInjection;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShop\PrestaShop\Core\Security\OAuth2\AuthorisationServerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Throwable;

/**
 * Adds main PrestaShop core services to the Symfony container.
 */
class PrestaShopExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $env = $container->getParameter('kernel.environment');
        $loader->load('services_' . $env . '.yml');

        // Automatically tag services that implements this interface
        $container->registerForAutoconfiguration(AuthorisationServerInterface::class)
            ->addTag('core.oauth2.authorization_server')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new AddOnsConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'prestashop';
    }

    public function prepend(ContainerBuilder $container)
    {
        $container->setParameter('prestashop.admin_cookie_lifetime', $this->getAdminCookieLifetime());
        $this->preprendApiConfig($container);
        $this->preprendSessionConfig($container);
    }

    protected function preprendSessionConfig(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('framework', [
            'session' => [
                'cookie_lifetime' => $this->getAdminCookieLifetime(),
                'cookie_samesite' => $this->getCookieSameSite(),
            ],
        ]);
    }

    protected function getCookieSameSite(): string
    {
        try {
            /** @var ConfigurationInterface $configuration */
            $configuration = new Configuration();
            $cookieSamesite = $configuration->get('PS_COOKIE_SAMESITE');
            $cookieSamesite = match ($cookieSamesite) {
                CookieOptions::SAMESITE_NONE => Cookie::SAMESITE_NONE,
                CookieOptions::SAMESITE_STRICT => Cookie::SAMESITE_STRICT,
                default => Cookie::SAMESITE_LAX,
            };
        } catch (Throwable) {
            $cookieSamesite = 'lax';
        }

        return $cookieSamesite;
    }

    protected function getAdminCookieLifetime(): int
    {
        try {
            /** @var ConfigurationInterface $configuration */
            $configuration = new Configuration();
            $cookieLifetimeBo = (int) $configuration->get('PS_COOKIE_LIFETIME_BO');
            if (empty($cookieLifetimeBo) || $cookieLifetimeBo <= 0) {
                $cookieLifetimeBo = CookieOptions::MAX_COOKIE_VALUE;
            }
        } catch (Throwable) {
            $cookieLifetimeBo = CookieOptions::MAX_COOKIE_VALUE;
        }

        // Configuration value (and default value) are expressed in HOURS, so we convert it into seconds
        return $cookieLifetimeBo * 3600;
    }

    protected function preprendApiConfig(ContainerBuilder $container)
    {
        $paths = [];
        $activeModules = $container->getParameter('prestashop.active_modules');
        $moduleDir = $container->getParameter('prestashop.module_dir');

        // We only load endpoints from active modules
        foreach ($activeModules as $moduleName) {
            $modulePath = $moduleDir . $moduleName;
            // Load YAML definition from the config/api_platform folder in the module
            $moduleConfigPath = sprintf('%s/config/api_platform', $modulePath);
            if (file_exists($moduleConfigPath)) {
                $paths[] = $moduleConfigPath;
            }

            // Load Doctrine entities that could be used as ApiPlatform DTO resources as well in the src/Entity folder
            $entitiesRessourcesPath = sprintf('%s/src/Entity', $modulePath);
            if (file_exists($entitiesRessourcesPath)) {
                // APIPlatform is looping on included resources and doing a require_once on those resources in ReflectionClassRecursiveIterator::getReflectionClassesFromDirectories.
                // This means that everything in those files is interpreted including the exit statement in some of those files ( especially in some index.php files used as an old way to make the directory read only ).
                // Since we cannot override or decorate the reflection class itself we have no other choice but to delete those files.
                if (file_exists($entitiesRessourcesPath . '/index.php')) {
                    unlink($entitiesRessourcesPath . '/index.php');
                }
                $paths[] = $entitiesRessourcesPath;
            }

            // Load ApiPlatform DTOs from the src/ApiPlatform/Resources folder
            $moduleRessourcesPath = sprintf('%s/src/ApiPlatform/Resources', $modulePath);
            if (file_exists($moduleRessourcesPath)) {
                $paths[] = $moduleRessourcesPath;
            }
        }

        if (!empty($paths)) {
            $container->prependExtensionConfig('api_platform', ['mapping' => ['paths' => $paths]]);
        }
    }
}
