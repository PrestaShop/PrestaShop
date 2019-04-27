<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

use Generator;
use Symfony\Component\DependencyInjection\Definition;

final class OptionsFormHookByServiceDefinitionProvider implements HookByServiceDefinitionProviderInterface
{
    const HOOK_NAME_POSITION_IN_CONSTRUCTOR = 4;

    const HOOK_NAME_STARTS_WITH = 'action';
    const HOOK_NAME_OF_FORM_BUILDER_ENDS_WITH = 'Form';
    const HOOK_NAME_OF_FORM_SAVE_ENDS_WIDTH = 'Save';

    /**
     * {@inheritdoc}
     */
    public function getHookNames(array $serviceDefinitions)
    {
        $optionNames = $this->getOptionNamesFromConstructorArgument($serviceDefinitions);

        $formBuilderHookNames = [];
        $formBuilderSaveHookNames = [];

        foreach ($optionNames as $optionName) {
            $formBuilderHookNames[] = $this->formatHookName(
                self::HOOK_NAME_STARTS_WITH,
                $optionName,
                self::HOOK_NAME_OF_FORM_BUILDER_ENDS_WITH
            );

            $formBuilderSaveHookNames[] = $this->formatHookName(
                self::HOOK_NAME_STARTS_WITH,
                $optionName,
                self::HOOK_NAME_OF_FORM_SAVE_ENDS_WIDTH
            );
        }

        return array_merge($formBuilderHookNames, $formBuilderSaveHookNames);
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
