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

use PrestaShopBundle\Form\Admin\Type\DisablingSwitchType;
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;
use PrestaShopBundle\Form\Extension\DisablingSwitchTrait;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * This listener is used by the DisablingSwitchExtension to automatically add the DisablingSwitchType to the parent,
 * it is not possible to access the parent builder in the extension which is why this operation is delayed on the
 * PRE_SET_DATA event.
 *
 * The switch state is automatically computed based on the input's data, if it matches the disabled_value then the field
 * is considered as disabled and the switch is turned off.
 *
 * Feature workflow:
 *  - DisablingExtension handles the option when disabling_switch is enabled, it registers this listener on the target field
 *  - on PRE_SET_DATA this listener:
 *    - gets the target field's parent form field
 *    - adds a new DisablingSwitchType on the parent
 *    - updates switch on/off state based on the option disabled_value and the data from the form
 *    - it also adds a data-toggled-by attribute on target fields so that JS can select the fields on FO
 *  - the DisablingSwitchExtension::buildView is in charge of adapting the target field(s) disabled attribute correctly
 *  - the prestashop UI kit form theme renders the DisablingSwitchType at the right place automatically
 *  - the DisablingSwitch js component handles the front behaviour of the feature
 */
class AddDisablingSwitchListener implements EventSubscriberInterface
{
    use DisablingSwitchTrait;

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
     * On PRE_SET_DATA event we add the disabling switch, we make sure it is only added once, and pre-set its disabled state
     * based on the target initial value and the switch configuration.
     *
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

        $shouldBeDisabled = $this->shouldFormBeDisabled($form, $event->getData());

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

        $this->addToggleAttribute($form, $disablingFieldName);
    }

    /**
     * @param FormInterface $form
     * @param string $disablingFieldName
     */
    private function addToggleAttribute(FormInterface $form, string $disablingFieldName): void
    {
        $formConfig = $form->getConfig();
        $newOptions = $formConfig->getOptions();
        if (empty($newOptions['attr'])) {
            $newOptions['attr'] = [];
        }

        // Add data attribute that allows the JS component to select associated components
        $newOptions['attr'][self::TOGGLE_DATA_ATTRIBUTE] = $disablingFieldName;

        $currentOptions = $formConfig->getOptions();
        $hasNewOptions = $newOptions !== $currentOptions;
        if ($hasNewOptions) {
            $newForm = $this->formCloner->cloneForm($form, $newOptions);
            $form->getParent()->add($newForm);
            $form = $newForm;
        }

        foreach ($form->all() as $childForm) {
            $this->addToggleAttribute($childForm, $disablingFieldName);
        }
    }
}
