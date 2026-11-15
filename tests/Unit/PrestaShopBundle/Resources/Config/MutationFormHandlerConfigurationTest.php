<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Resources\Config;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertiesFormDataPersister;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Mutation\BackOfficeMutationLogger;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class MutationFormHandlerConfigurationTest extends TestCase
{
    public function testProductAndCategoryFormHandlersOptInToMutationLogging(): void
    {
        $configuration = Yaml::parseFile(
            dirname(__DIR__, 5) . '/src/PrestaShopBundle/Resources/config/services/core/form/form_handler.yml'
        );

        $services = $configuration['services'];

        $this->assertSame(
            'product',
            $services['prestashop.core.form.identifiable_object.product_form_handler']['arguments'][1]
        );
        $this->assertSame(
            'category',
            $services['prestashop.core.form.identifiable_object.handler.category_form_handler']['arguments'][1]
        );
        $this->assertSame(
            'category',
            $services['prestashop.core.form.identifiable_object.handler.root_category_form_handler']['arguments'][1]
        );
    }

    public function testFormHandlerConfigurationCompilesWithMutationLogger(): void
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 5)));
        $loader->load('src/PrestaShopBundle/Resources/config/services/core/form/form_handler.yml');
        $factoryId = 'prestashop.core.form.identifiable_object.handler.form_handler_factory';
        // External dependencies are synthetic; demo mode is not relevant to wiring.
        $container->getDefinition($factoryId)->setArgument(2, false);
        $classes = [
            'prestashop.core.hook.dispatcher' => HookDispatcherInterface::class,
            'translator' => TranslatorInterface::class,
            ExtraPropertiesFormDataPersister::class => ExtraPropertiesFormDataPersister::class,
            BackOfficeMutationLogger::class => BackOfficeMutationLogger::class,
        ];
        foreach ($container->getDefinitions() as $definition) {
            foreach ($definition->getArguments() as $argument) {
                if ($argument instanceof Reference && !$container->has((string) $argument)) {
                    $id = (string) $argument;
                    $container->setDefinition($id, (new Definition($classes[$id] ?? FormDataHandlerInterface::class))->setSynthetic(true));
                }
            }
        }
        $container->compile();
        $this->assertSame(
            BackOfficeMutationLogger::class,
            (string) $container->getDefinition($factoryId)->getArgument(4)
        );
    }

    public function testUnrelatedFormHandlerDoesNotOptInImplicitly(): void
    {
        $configuration = Yaml::parseFile(
            dirname(__DIR__, 5) . '/src/PrestaShopBundle/Resources/config/services/core/form/form_handler.yml'
        );

        $arguments = $configuration['services']['prestashop.core.form.identifiable_object.handler.contact_form_handler']['arguments'];

        $this->assertCount(1, $arguments);
    }
}
