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
     * ApiPlatform reads a mapping path with ReflectionClassRecursiveIterator, which requires a
     * directory and does a require_once on every PHP file it finds there. A module index.php holds
     * an `exit` statement, so including it stops the whole build - and it cannot be caught, since
     * the iterator only guards against Throwable.
     *
     * Rather than delete files from a directory the shop does not own, which is what used to happen
     * here and what made those files show up as missing during an update, we point ApiPlatform at a
     * directory of our own holding one link per entity file. Symlinks resolve back to the module
     * file, so a class is still loaded from its own path; copying is the fallback where linking is
     * not available.
     *
     * @return string the directory to hand to ApiPlatform
     */
    private function mirrorModuleEntities(ContainerBuilder $container, string $moduleName, string $entitiesPath): string
    {
        $mirrorPath = sprintf('%s/api_platform/module_entities/%s', $container->getParameter('kernel.cache_dir'), $moduleName);

        $filesystem = new Filesystem();
        $filesystem->remove($mirrorPath);
        $filesystem->mkdir($mirrorPath);

        $entityFiles = Finder::create()
            ->files()
            ->in($entitiesPath)
            ->name('*.php')
            ->notName('index.php');

        foreach ($entityFiles as $entityFile) {
            $target = $mirrorPath . DIRECTORY_SEPARATOR . $entityFile->getRelativePathname();
            $filesystem->mkdir(dirname($target));

            try {
                $filesystem->symlink($entityFile->getRealPath(), $target);
            } catch (IOException $e) {
                $filesystem->copy($entityFile->getRealPath(), $target);
            }
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
                $paths[] = $this->mirrorModuleEntities($container, $moduleName, $entitiesRessourcesPath);
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
