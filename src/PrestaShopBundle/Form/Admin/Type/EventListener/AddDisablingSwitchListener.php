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
            throw new InvalidConfigurationException(sprintf('You cannot set the option %s on a root form.', DisablingSwitchExtension::FORM_OPTION));
        }

        $disablingFieldName = DisablingSwitchExtension::FIELD_PREFIX . $form->getName();
        if ($parent->has($disablingFieldName)) {
            return;
        }

        $disabledValue = $form->getConfig()->getOption('disabled_value');
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
        $disabledData = $shouldBeDisabled ? self::DISABLED_VALUE : self::ENABLED_VALUE;
        $disablingSwitchOptions = [
            // All associated form fields have been added with a data attribute which allows to target them all
            'target_selector' => sprintf('[%s="%s"]', self::TOGGLE_DATA_ATTRIBUTE, $disablingFieldName),
            'data' => $disabledData,
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
