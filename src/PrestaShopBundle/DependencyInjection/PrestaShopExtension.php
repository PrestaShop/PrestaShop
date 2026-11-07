<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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

    /**
     * ApiPlatform requires every PHP file under a mapping path, and a module index.php ends with `exit`.
     * Symlinks keep each class resolving to its module file, the one Doctrine and the autoloader load.
     *
     * @return string|null the directory to map, null when the entities cannot be mapped safely
     */
    private function getModuleEntitiesMappingPath(ContainerBuilder $container, string $moduleName, string $entitiesPath): ?string
    {
        if (!Finder::create()->files()->in($entitiesPath)->name('index.php')->hasResults()) {
            return $entitiesPath;
        }

        $mirrorPath = sprintf('%s/api_platform/module_entities/%s', $container->getParameter('kernel.cache_dir'), $moduleName);
        $filesystem = new Filesystem();

        try {
            $filesystem->remove($mirrorPath);
            $filesystem->mkdir($mirrorPath);

            $entityFiles = Finder::create()
                ->files()
                ->in($entitiesPath)
                ->name('*.php')
                ->notName('index.php');

            foreach ($entityFiles as $entityFile) {
                $filesystem->symlink($entityFile->getRealPath(), $mirrorPath . DIRECTORY_SEPARATOR . $entityFile->getRelativePathname());
            }
        } catch (IOException) {
            return null;
        }

        return $mirrorPath;
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
                $entitiesMappingPath = $this->getModuleEntitiesMappingPath($container, $moduleName, $entitiesRessourcesPath);
                if (null !== $entitiesMappingPath) {
                    $paths[] = $entitiesMappingPath;
                }
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
