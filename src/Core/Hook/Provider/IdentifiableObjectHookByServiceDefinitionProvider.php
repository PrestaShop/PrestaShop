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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Gets hook names by using identifiable object form builder service definition.
 */
final class IdentifiableObjectHookByServiceDefinitionProvider implements HookByServiceDefinitionProviderInterface
{
    const FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER = 0;

    const FORM_BUILDER_HOOK_STARTS_WITH = 'action';
    const FORM_BUILDER_HOOK_ENDS_WITH = 'FormBuilderModifier';

    const FORM_HANDLER_UPDATE_BEFORE_STARTS_WITH = 'actionBeforeUpdate';
    const FORM_HANDLER_UPDATE_AFTER_STARTS_WITH = 'actionAfterUpdate';
    const FORM_HANDLER_CREATE_BEFORE_STARTS_WITH = 'actionBeforeCreate';
    const FORM_HANDLER_CREATE_AFTER_STARTS_WITH = 'actionAfterCreate';
    const FORM_HANDLER_ENDS_WITH = 'FormHandler';

    /**
     * @var FormRegistryInterface
     */
    private $formFactory;


    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getHookNames(array $serviceDefinitions)
    {
        $formNames = $this->getFormNames($serviceDefinitions);

        $formBuilderHookNames = [];
        $formHandlerBeforeUpdateHookNames = [];
        $formHandlerAfterUpdateHookNames = [];
        $formHandlerBeforeCreateHookNames = [];
        $formHandlerAfterCreateHookNames = [];

        foreach ($formNames as $formName) {
            $formBuilderHookNames[] = $this->formatHookName(
                self::FORM_BUILDER_HOOK_STARTS_WITH,
                $formName,
                self::FORM_BUILDER_HOOK_ENDS_WITH
            );

            $formHandlerBeforeUpdateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_UPDATE_BEFORE_STARTS_WITH,
                $formName,
                self::FORM_HANDLER_ENDS_WITH
            );

            $formHandlerAfterUpdateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_UPDATE_AFTER_STARTS_WITH,
                $formName,
                self::FORM_HANDLER_ENDS_WITH
            );

            $formHandlerBeforeCreateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_CREATE_BEFORE_STARTS_WITH,
                $formName,
                self::FORM_HANDLER_ENDS_WITH
            );

            $formHandlerAfterCreateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_CREATE_AFTER_STARTS_WITH,
                $formName,
                self::FORM_HANDLER_ENDS_WITH
            );
        }

        return array_merge(
            $formBuilderHookNames,
            $formHandlerBeforeUpdateHookNames,
            $formHandlerAfterUpdateHookNames,
            $formHandlerBeforeCreateHookNames,
            $formHandlerAfterCreateHookNames
        );
    }

    /**
     * Gets form names which are used when generating hooks.
     *
     * @param Definition[] $serviceDefinitions
     *
     * @return Generator
     */
    private function getFormNames(array $serviceDefinitions)
    {
        foreach ($serviceDefinitions as $serviceDefinition) {
            $formType = $serviceDefinition->getArgument(self::FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER);

            if (!is_string($formType) || !is_subclass_of($formType, FormTypeInterface::class)) {
                continue;
            }

            yield $this->formFactory->createBuilder($formType)->getName();
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
        //todo: camelise this if such functionality will be introduced to 1.7.6
        return $hookStartsWith . $hookId . $hookEndsWidth;
    }
}
