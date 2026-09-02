<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Container;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShopBundle\DependencyInjection\Compiler\ModulesDoctrineCompilerPass;
use PrestaShopBundle\DependencyInjection\Config\ConfigYamlLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DoctrineBuilderExtension is used to init the doctrine service in the ContainerBuilder.
 * This is a manual initialisation of Doctrine because we are not in a symfony context, so we need
 * add the extension manually (required parameters are managed by ContainerParametersExtension).
 *
 * Note: this can't be done as a CompilerPassInterface because extensions need to be registered before compilation.
 */
class DoctrineBuilderExtension implements ContainerBuilderExtensionInterface
{
    /** @var EnvironmentInterface */
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
        $configDirectories = [$container->getParameter('kernel.project_dir') . '/app/config'];
        $fileLocator = new FileLocator($configDirectories);

        $configLoader = new ConfigYamlLoader($fileLocator);
        $configPath = sprintf('config_legacy_%s.yml', $this->environment->getName());
        $configLoader->load($configPath);
        $config = $configLoader->getConfig();

        $container->registerExtension(new DoctrineExtension());
        $container->loadFromExtension('doctrine', $config['doctrine']);
        $container->addCompilerPass(new ModulesDoctrineCompilerPass());
    }
}
