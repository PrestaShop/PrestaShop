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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type\EventListener;

use PrestaShopBundle\Form\Admin\Extension\DisablingSwitchExtension;
use PrestaShopBundle\Form\Admin\Type\DisablingSwitchType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use TypeError;

/**
 * This listener is used by the DisablingSwitchExtension to automatically add the DisablingSwitchType to the parent,
 * it is not possible to access the parent builder in the extension which is why this operation is delayed on the
 * PRE_SET_DATA event.
 *
 * The switch state and the associated input's disabled state are automatically computed based on the input's data,
 * if it matches the disabled_value then the field is considered as disabled and the switch is turned off.
 *
 * Feature workflow:
 *  - DisablingExtension handles the option when disabling_switch is enabled, it registers this listener on the target field
 *  - on PRE_SET_DATA this listener:
 *    - gets the target field's parent form field
 *    - adds a new DisablingSwitchType on the parent
 *    - updates the target's field disable attribute based on the option disabled_value
 *    - it also adds a data-toggled-by attribute so that JS can select the fields on FO
 *  - the prestashop UI kit form theme renders the DisablingSwitchType at the right place automatically
 *  - the DisablingSwitch js component handles the front behaviour of the feature
 */
class AddDisablingSwitchListener implements EventSubscriberInterface
{
    public const TOGGLE_DATA_ATTRIBUTE = 'data-toggled-by';

    private const DISABLED_VALUE = '0';
    private const ENABLED_VALUE = '1';

    /**
     * @var FormCloner
     */
    private $formCloner;

    public function __construct(FormCloner $formCloner)
    {
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'addDisablingSwitch',
        ];
    }

    /**
     * @param PreSetDataEvent $event
     */
    public function addDisablingSwitch(PreSetDataEvent $event): void
    {
        $form = $event->getForm();
        $parent = $form->getParent();
        if (null === $parent) {
            throw new InvalidConfigurationException(sprintf('You cannot set the option %s on a root form.', DisablingSwitchExtension::SWITCH_OPTION));
        }

        $disablingFieldName = DisablingSwitchExtension::FIELD_PREFIX . $form->getName();
        if ($parent->has($disablingFieldName)) {
            return;
        }

        $disabledValue = $form->getConfig()->getOption(DisablingSwitchExtension::DISABLED_VALUE_OPTION);
        if (is_callable($disabledValue)) {
            try {
                $shouldBeDisabled = $disabledValue($event->getData(), $event->getForm());
            } catch (TypeError $typeError) {
                throw new InvalidConfigurationException(
                    'The callable provided for disabled_value option seems invalid, its prototype should be compatible with function($data, FormInterface $form): void And $data is usually nullable',
                    0,
                    $typeError
                );
            }
        } else {
            if (null === $disabledValue) {
                $disabledValue = $form->getConfig()->getOption('default_empty_data');
            }
            if (null === $disabledValue) {
                $emptyData = $form->getConfig()->getOption('empty_data');
                $disabledValue = $emptyData instanceof \Closure ? $emptyData($form) : $emptyData;
            }

            $shouldBeDisabled = $disabledValue === $event->getData();
        }

        // If field should be disabled then the toggle value should be 0
        $switchStateOnDisable = $form->getConfig()->getOption(DisablingSwitchExtension::SWITCH_STATE_ON_DISABLE_OPTION);

        // Define how the component behaves when it is in the disabled state (display off or on)
        $disableOnMatch = $switchStateOnDisable === 'off';
        if ($disableOnMatch) {
            $disabledData = $shouldBeDisabled ? self::DISABLED_VALUE : self::ENABLED_VALUE;
        } else {
            $disabledData = $shouldBeDisabled ? self::ENABLED_VALUE : self::DISABLED_VALUE;
        }

        $disablingSwitchOptions = [
            // All associated form fields have been added with a data attribute which allows to target them all
            'target_selector' => sprintf('[%s="%s"]', self::TOGGLE_DATA_ATTRIBUTE, $disablingFieldName),
            'switch_event' => $form->getConfig()->getOption(DisablingSwitchExtension::SWITCH_EVENT_OPTION),
            'data' => $disabledData,
            'disable_on_match' => $disableOnMatch,
        ];

        $parent->add(
            $disablingFieldName,
            DisablingSwitchType::class,
            $disablingSwitchOptions
        );

        $this->updateFormInitialDisableState($form, $shouldBeDisabled, $disablingFieldName);
    }

    private function updateFormInitialDisableState(FormInterface $form, bool $shouldBeDisabled, string $disablingFieldName): void
    {
        foreach ($form->all() as $childForm) {
            $this->updateFormInitialDisableState($childForm, $shouldBeDisabled, $disablingFieldName);
        }

        $formConfig = $form->getConfig();
        $newOptions = $formConfig->getOptions();
        if (empty($newOptions['attr'])) {
            $newOptions['attr'] = [];
        }

        // Add data attribute that allows the JS component to select associated components
        $newOptions['attr'][self::TOGGLE_DATA_ATTRIBUTE] = $disablingFieldName;

        // We only set the HTML attribute not the form field option disabled, or else its value will be ignored and
        // won't be part of the form submitted data The field is disabled in the FO via this initial state and potentially
        // JS manipulation (when the switch is used), in which case the form submitted data will be replaced by the field's
        // default_empty_data option (or empty_data if the default_empty_data option is not present)
        $newOptions['attr']['disabled'] = $shouldBeDisabled;

        $currentOptions = $formConfig->getOptions();
        $hasNewOptions = $newOptions !== $currentOptions;
        if ($hasNewOptions) {
            $newForm = $this->formCloner->cloneForm($form, $newOptions);
            $form->getParent()->add($newForm);
        }
    }
}
