<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Container;

use PrestaShop\PrestaShop\Adapter\Module\Repository\CachedModuleRepository;
use PrestaShop\PrestaShop\Adapter\Module\Repository\ModuleRepository;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This container extension is in charge of initializing the container parameters.
 * It uses the same type of init as the kernel container thanks to the set_parameters.php
 * script which allows it to be as close as possible to the symfony kernel AND the script
 * automatically manage env test switching.
 *
 * We also add a few default parameters which are expected by doctrine and some of our
 * compiler pass which need the list of active modules.
 *
 * Note: this can't be done as a CompilerPassInterface because parameters need to be set before extensions
 * are registered (especially Doctrine extension).
 */
class ContainerParametersExtension implements ContainerBuilderExtensionInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     */
    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        // This script is used in config.yml to init the container parameters
        // It is also able to generate the parameters.php file if it does not exist
        include _PS_ROOT_DIR_ . '/app/config/set_parameters.php';
        $container->addResource(new FileResource(_PS_ROOT_DIR_ . '/app/config/parameters.php'));

        // Most of these parameters are just necessary from doctrine services definitions
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.debug', $this->environment->isDebug());
        $container->setParameter('kernel.environment', $this->environment->getName());
        $container->setParameter('kernel.app_id', $this->environment->getAppId());

        // Note: this is not the same folder in test env because PS_CACHE_DIR only manages dev and prod env
        // but it should! So for now let's do it the right way here and let's fix the rest later when EnvironmentInterface
        // will be correctly/fully integrated.
        $container->setParameter('kernel.cache_dir', $this->environment->getCacheDir());

        // Init the active modules
        if ($this->environment->getName() === 'test') {
            $cache = new NullAdapter();
        } else {
            $cache = new FilesystemAdapter('modules', 0, $this->environment->getCacheDir());
        }
        $moduleRepository = new CachedModuleRepository(
            new ModuleRepository(_PS_ROOT_DIR_, _PS_MODULE_DIR_),
            $cache
        );
        $activeModules = $moduleRepository->getActiveModules();
        /* @deprecated kernel.active_modules is deprecated. Use prestashop.active_modules instead. */
        $container->setParameter('kernel.active_modules', $activeModules);
        $container->setParameter('prestashop.active_modules', $activeModules);
        $container->setParameter('prestashop.installed_modules', $moduleRepository->getInstalledModules());
        $container->setParameter('prestashop.module_dir', _PS_MODULE_DIR_);

        if (!$container->hasParameter('kernel.project_dir')) {
            $container->setParameter('kernel.project_dir', _PS_ROOT_DIR_);
        }
    }
}
