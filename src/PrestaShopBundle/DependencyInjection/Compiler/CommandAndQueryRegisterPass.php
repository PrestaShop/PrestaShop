<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection\Compiler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use ReflectionClass;
use ReflectionNamedType;
use Reflector;
use RuntimeException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This class automatically configures the 'AsCommandHandler' and 'AsQueryHandler' attributes
 * as tags for auto-detection in Symfony.
 *
 * Classes marked with the 'AsCommandHandler' attribute will be registered as command handlers,
 * while classes marked with 'AsQueryHandler' will be registered as query handlers.
 *
 * To make this work, make sure to add the appropriate annotations to the classes that need to
 * be detected as command or query handlers.
 *
 * Usage example:
 *
 *     #[AsCommandHandler]
 *     class MyCommandHandler
 *     {
 *         // ...
 *     }
 *
 *     #[AsQueryHandler]
 *     class MyQueryHandler
 *     {
 *         // ...
 *     }
 *
 * These classes will be automatically discovered and registered in the Symfony service container
 * using the `registerAttributeForAutoconfiguration` method.
 *
 * @see https://symfony.com/doc/current/service_container/tags.html#autoconfiguring-tags-with-attributes
 */
class CommandAndQueryRegisterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $this->registerMessengerTags($container);
    }

    /**
     * register messenger tags allowing the recognition of handlers by symfony
     */
    private function registerMessengerTags(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition, AsCommandHandler $attribute, Reflector $reflector): void {
            if (!$reflector instanceof ReflectionClass) {
                throw new RuntimeException(sprintf('AsCommandHandler must be a class attribute, "%s" given.', $reflector::class));
            }
            $definition->addTag('messenger.message_handler', ['method' => $attribute->method, 'handles' => self::guessHandledClasses($reflector, $attribute->method)]);
        });

        $container->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (ChildDefinition $definition, AsQueryHandler $attribute, Reflector $reflector): void {
            if (!$reflector instanceof ReflectionClass) {
                throw new RuntimeException(sprintf('AsQueryHandler must be a class attribute, "%s" given.', $reflector::class));
            }
            $definition->addTag('messenger.message_handler', ['method' => $attribute->method, 'handles' => self::guessHandledClasses($reflector, $attribute->method)]);
        });
    }

    private static function guessHandledClasses(ReflectionClass $class, string $method): string
    {
        $reflectionMethod = $class->getMethod($method);
        $parameters = $reflectionMethod->getParameters();

        if (count($parameters) != 1) {
            throw new RuntimeException(sprintf('Invalid handler service "%s": number of argument "$%s" in method "%s" must be 1 , "%s" given.', $class->getName(), $parameters[0]->getName(), $method, count($parameters)));
        }

        $firstParameter = $parameters[0]->getType();
        if (!$firstParameter instanceof ReflectionNamedType) {
            throw new RuntimeException(sprintf('Invalid parameter type: must be ReflectionNamedType , "%s" given.', $firstParameter::class));
        }

        return $firstParameter->getName();
    }
}
