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

use Generator;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Used for collecting options form hook names and store them in the container. Options form hook name is stored in the
 * constructor argument. E.g in yml file:
 *
 * prestashop.admin.my_form.form_handler:
 *   class: 'MyForm'
 *   arguments:
 *    - '@someService'
 *    - 'MyFormHookName'
 *
 * In the sample, hook name is located in 1 position of argument array.
 */
final class OptionsFormHookNameCollectorPass implements CompilerPassInterface
{
    const OPTIONS_FORM_SERVICE_SUFFIX = 'form_handler';

    const HOOK_NAME_POSITION_IN_CONSTRUCTOR = 4;
    const HOOK_NAME_PREFIX = 'action';
    const HOOK_NAME_OF_FORM_BUILDER_SUFFIX = 'Form';
    const HOOK_NAME_OF_FORM_SAVE_SUFFIX = 'Save';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!in_array($container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return;
        }

        $serviceDefinitions = $container->getDefinitions();

        $optionsFormServiceDefinitions = [];
        foreach ($serviceDefinitions as $serviceId => $serviceDefinition) {
            if ($serviceDefinition->isAbstract() || $serviceDefinition->isPrivate()) {
                continue;
            }

            if ($this->isOptionsFormService($serviceId, $serviceDefinition->getClass())) {
                $optionsFormServiceDefinitions[$serviceId] = $serviceDefinition;
            }
        }

        $optionNames = $this->getOptionNamesFromConstructorArgument($optionsFormServiceDefinitions);

        $formBuilderHookNames = [];
        $formBuilderSaveHookNames = [];

        foreach ($optionNames as $optionName) {
            $formBuilderHookNames[] = $this->formatHookName(
                self::HOOK_NAME_PREFIX,
                $optionName,
                self::HOOK_NAME_OF_FORM_BUILDER_SUFFIX
            );

            $formBuilderSaveHookNames[] = $this->formatHookName(
                self::HOOK_NAME_PREFIX,
                $optionName,
                self::HOOK_NAME_OF_FORM_SAVE_SUFFIX
            );
        }

        $container->setParameter(
            'prestashop.hook.option_form_hook_names',
            array_merge($formBuilderHookNames, $formBuilderSaveHookNames)
        );
    }

    /**
     * Checks if service belongs to options form.
     *
     * @param string $serviceId
     * @param string $serviceClass
     *
     * @return bool
     */
    private function isOptionsFormService($serviceId, $serviceClass)
    {
        return $this->stringEndsWith($serviceId, self::OPTIONS_FORM_SERVICE_SUFFIX) &&
            is_subclass_of($serviceClass, FormHandlerInterface::class)
        ;
    }

    /**
     * Checks if string ends with certain string.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    private function stringEndsWith($haystack, $needle)
    {
        $diff = strlen($haystack) - strlen($needle);

        return $diff >= 0 && strpos($haystack, $needle, $diff) !== false;
    }

    /**
     * @param Definition[] $serviceDefinitions
     *
     * @return Generator
     */
    private function getOptionNamesFromConstructorArgument(array $serviceDefinitions)
    {
        foreach ($serviceDefinitions as $serviceDefinition) {
            $constructorArguments = $serviceDefinition->getArguments();

            if (!isset($constructorArguments[self::HOOK_NAME_POSITION_IN_CONSTRUCTOR])) {
                continue;
            }

            $hookName = $constructorArguments[self::HOOK_NAME_POSITION_IN_CONSTRUCTOR];

            if (!is_string($hookName)) {
                continue;
            }

            yield $hookName;
        }
    }

    /**
     * Formats hook names.
     *
     * @param string $hookStartsWith
     * @param string $hookId
     * @param string $hookEndsWidth
     *
     * @return string
     */
    private function formatHookName($hookStartsWith, $hookId, $hookEndsWidth)
    {
        return $hookStartsWith . $hookId . $hookEndsWidth;
    }
}
