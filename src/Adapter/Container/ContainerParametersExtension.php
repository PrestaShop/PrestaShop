<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Container;

use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShopBundle\Kernel\ModuleRepository;
use PrestaShopBundle\Kernel\ModuleRepositoryFactory;
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
        //This script is used in config.yml to init the container parameters
        //It is also able to generate the parameters.php file if it does not exist
        include _PS_ROOT_DIR_ . '/app/config/set_parameters.php';
        $container->addResource(new FileResource(_PS_ROOT_DIR_ . '/app/config/parameters.php'));

        //Most of these parameters are just necessary fro doctrine services definitions
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.root_dir', _PS_ROOT_DIR_ . '/app/');
        $container->setParameter('kernel.name', 'app');
        $container->setParameter('kernel.debug', $this->environment->isDebug());
        $container->setParameter('kernel.environment', $this->environment->getName());

        //Note: this is not the same folder in test env because PS_CACHE_DIR only manages dev and prod env
        //but it should! So for now let's do it the right way here and let's fix the rest later when EnvironmentInterface
        //will be correctly/fully integrated.
        $container->setParameter('kernel.cache_dir', $this->environment->getCacheDir());

        //Init the active modules
        $factory = new ModuleRepositoryFactory($container->getParameterBag()->all());
        /** @var ModuleRepository $moduleRepository */
        $moduleRepository = $factory->getRepository();
        $container->setParameter('kernel.active_modules', $moduleRepository->getActiveModules());
    }
}
