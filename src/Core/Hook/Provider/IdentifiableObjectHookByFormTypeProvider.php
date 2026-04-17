<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook\Provider;

use Exception;
use Generator;
use Logger;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Throwable;

/**
 * Gets hook names by identifiable object form types.
 */
final class IdentifiableObjectHookByFormTypeProvider implements HookByFormTypeProviderInterface
{
    public const FORM_TYPE_POSITION_IN_CONSTRUCTOR_OF_FORM_BUILDER = 0;

    public const FORM_BUILDER_HOOK_PREFIX = 'action';
    public const FORM_BUILDER_HOOK_SUFFIX = 'FormBuilderModifier';
    private const FORM_BUILDER_HOOK_SUFFIX_DATA_PROVIDER_DATA = 'FormDataProviderData';
    private const FORM_BUILDER_HOOK_SUFFIX_DATA_PROVIDER_DEFAULT_DATA = 'FormDataProviderDefaultData';

    public const FORM_HANDLER_UPDATE_BEFORE_PREFIX = 'actionBeforeUpdate';
    public const FORM_HANDLER_UPDATE_AFTER_PREFIX = 'actionAfterUpdate';
    public const FORM_HANDLER_CREATE_BEFORE_PREFIX = 'actionBeforeCreate';
    public const FORM_HANDLER_CREATE_AFTER_PREFIX = 'actionAfterCreate';
    public const FORM_HANDLER_SUFFIX = 'FormHandler';

    /**
     * @var FormFactoryInterface
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
    public function getHookNames(array $formTypes)
    {
        $formNames = $this->getFormNames($formTypes);

        $formBuilderHookNames =
            $formBuilderDataProviderDataHookNames =
            $formBuilderDataProviderDefaultDataHookNames =
            $formHandlerBeforeUpdateHookNames =
            $formHandlerAfterUpdateHookNames =
            $formHandlerBeforeCreateHookNames =
            $formHandlerAfterCreateHookNames = [];

        foreach ($formNames as $formName) {
            $formBuilderHookNames[] = $this->formatHookName(
                self::FORM_BUILDER_HOOK_PREFIX,
                $formName,
                self::FORM_BUILDER_HOOK_SUFFIX
            );

            $formBuilderDataProviderDataHookNames[] = $this->formatHookName(
                self::FORM_BUILDER_HOOK_PREFIX,
                $formName,
                self::FORM_BUILDER_HOOK_SUFFIX_DATA_PROVIDER_DATA
            );

            $formBuilderDataProviderDefaultDataHookNames[] = $this->formatHookName(
                self::FORM_BUILDER_HOOK_PREFIX,
                $formName,
                self::FORM_BUILDER_HOOK_SUFFIX_DATA_PROVIDER_DEFAULT_DATA
            );

            $formHandlerBeforeUpdateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_UPDATE_BEFORE_PREFIX,
                $formName,
                self::FORM_HANDLER_SUFFIX
            );

            $formHandlerAfterUpdateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_UPDATE_AFTER_PREFIX,
                $formName,
                self::FORM_HANDLER_SUFFIX
            );

            $formHandlerBeforeCreateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_CREATE_BEFORE_PREFIX,
                $formName,
                self::FORM_HANDLER_SUFFIX
            );

            $formHandlerAfterCreateHookNames[] = $this->formatHookName(
                self::FORM_HANDLER_CREATE_AFTER_PREFIX,
                $formName,
                self::FORM_HANDLER_SUFFIX
            );
        }

        return array_merge(
            $formBuilderHookNames,
            $formBuilderDataProviderDataHookNames,
            $formBuilderDataProviderDefaultDataHookNames,
            $formHandlerBeforeUpdateHookNames,
            $formHandlerAfterUpdateHookNames,
            $formHandlerBeforeCreateHookNames,
            $formHandlerAfterCreateHookNames
        );
    }

    /**
     * Gets form names which are used when generating hooks.
     *
     * @param string[] $formTypes
     *
     * @return Generator
     */
    private function getFormNames(array $formTypes)
    {
        foreach ($formTypes as $formType) {
            try {
                yield $this->formFactory->createBuilder($formType)->getName();
            } catch (Exception $e) {
                Logger::addLog(sprintf('Error while loading formType: %s . Error: %s', $formType, $e));
            } catch (Throwable $e) {
                Logger::addLog(sprintf('Invalid argument exception: %s . Error: %s', $formType, $e));
            }
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
        return $hookStartsWith . Container::camelize($hookId) . $hookEndsWidth;
    }
}
