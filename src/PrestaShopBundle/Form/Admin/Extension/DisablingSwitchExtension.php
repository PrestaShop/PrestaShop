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

namespace PrestaShopBundle\Form\Admin\Extension;

use Closure;
use PrestaShopBundle\Form\Admin\Type\DisablingSwitchType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This extension allows you to associate a switch disabler component to a form type by setting
 * the option disabling_switch to true. The component is automatically added to the form thanks to
 * a form listener and automatically rendered in the prestashop form theme (although you can always
 * override the rendering if needed).
 *
 * The switch state is detected based on your input empty data but you can also define a more accurate
 * disabled value if needed thanks to the disabled_value option.
 */
class DisablingSwitchExtension extends AbstractTypeExtension
{
    public const FIELD_PREFIX = 'disabling_switch_';

    public const SWITCH_OPTION = 'disabling_switch';
    public const SWITCH_EVENT_OPTION = 'disabling_switch_event';
    public const DISABLED_VALUE_OPTION = 'disabled_value';
    public const SWITCH_STATE_ON_DISABLE_OPTION = 'switch_state_on_disable';

    /**
     * @var EventSubscriberInterface
     */
    private $addDisablingSwitchListener;

    public function __construct(
        EventSubscriberInterface $addDisablingSwitchListener
    ) {
        $this->addDisablingSwitchListener = $addDisablingSwitchListener;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [
            // To add the option on all form types
            FormType::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // This particular field has the expected option enabled, so we assign the add listener to dynamically add the
        // associated DisablingSwitchType to the parent
        $hasToggleOption = $builder->getOption(self::SWITCH_OPTION);
        if ($hasToggleOption) {
            $builder->addEventSubscriber($this->addDisablingSwitchListener);
        }
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars[self::SWITCH_OPTION] = $options[self::SWITCH_OPTION];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                self::SWITCH_OPTION => false,
                // We use this value to know if the field state is disabled or not on first rendering, if the value is null
                // we have other fallback options, the priority is:
                //   - disabled_value
                //   - default_empty_data
                //   - empty_data
                //
                // We then compare this value with the form data, if the two values match (strict equality) then the field
                // is considered disabled and the input will be assigned the disabled attribute.
                //
                // You can also specify a callback or a closure for this option this allows more complex use case to define
                // if the form is considered empty/disabled or not (useful for compound form based on multiple values).
                // The callback will receive the form data and the FormInterface, it must return true if the field should
                // be disabled. Keep in mind that the data, which comes from the Form event, can be null quite often, so
                // your callback should accept nullable data.
                //
                // ex: 'disabled_value' => function (?array $data, FormInterface $form): bool {
                //          return empty($data['reduction_type']) || empty($data['reduction_value']);
                //      },
                self::DISABLED_VALUE_OPTION => null,
                // You can define an JS event triggered on witch changes
                self::SWITCH_EVENT_OPTION => null,
                // Define the state of the switch component when value is disabled (by default on off)
                self::SWITCH_STATE_ON_DISABLE_OPTION => 'off',
            ])
            ->setAllowedTypes(self::SWITCH_OPTION, 'bool')
            ->setAllowedTypes(self::DISABLED_VALUE_OPTION, ['null', 'string', 'int', 'array', 'object', 'bool', 'float', 'callback', Closure::class])
            ->setAllowedTypes(self::SWITCH_EVENT_OPTION, ['string', 'null'])
            ->setAllowedTypes(self::SWITCH_STATE_ON_DISABLE_OPTION, 'string')
            ->setAllowedValues(self::SWITCH_STATE_ON_DISABLE_OPTION, ['off', 'on'])
        ;
    }
}
