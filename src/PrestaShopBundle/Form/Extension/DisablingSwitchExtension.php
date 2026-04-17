<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Closure;
use PrestaShopBundle\Form\Admin\Type\EventListener\AddDisablingSwitchListener;
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
    use DisablingSwitchTrait;

    public const FIELD_PREFIX = 'disabling_switch_';

    public const SWITCH_OPTION = 'disabling_switch';
    public const SWITCH_EVENT_OPTION = 'disabling_switch_event';
    public const DISABLED_VALUE_OPTION = 'disabled_value';
    public const SWITCH_STATE_ON_DISABLE_OPTION = 'switch_state_on_disable';
    public const SWITCH_LABEL = 'disabling_switch_label';

    /**
     * @var AddDisablingSwitchListener
     */
    private $addDisablingSwitchListener;

    public function __construct(
        AddDisablingSwitchListener $addDisablingSwitchListener
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // This particular field has the expected option enabled, so we assign the add listener to dynamically add the
        // associated DisablingSwitchType to the parent
        $hasToggleOption = $builder->getOption(static::SWITCH_OPTION);
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
        $switchableParent = $this->getSwitchableParent($form);
        if ($switchableParent) {
            // When rendering a prototype we must not force the disabled attribute in any way, because it will be hard-coded
            // in all the future elements rendered dynamically thanks to the prototype template
            if ($this->isRenderingPrototype($form)) {
                return;
            }

            $disablingFieldName = self::FIELD_PREFIX . $switchableParent->getName();
            $parent = $switchableParent->getParent();
            if ($parent->has($disablingFieldName)) {
                $shouldBeDisabled = $this->shouldFormBeDisabled($switchableParent, $switchableParent->getData());

                // We only set the HTML attribute not the form field option disabled, or else its value will be ignored and
                // won't be part of the form submitted data, that's why we only set this attribute for the view This will
                // correctly set the input state on initial rendering and even on submit since buildView happens after the
                // submitted data is handled by the form
                $view->vars['attr']['disabled'] = $shouldBeDisabled;
            }
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                static::SWITCH_OPTION => false,
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
                static::DISABLED_VALUE_OPTION => null,
                // You can define an JS event triggered on which changes
                static::SWITCH_EVENT_OPTION => null,
                // Define the state of the switch component when value is disabled (by default on off)
                static::SWITCH_STATE_ON_DISABLE_OPTION => 'off',
                // Define the label of the switch component if needed
                static::SWITCH_LABEL => false,
            ])
            ->setAllowedTypes(static::SWITCH_OPTION, 'bool')
            ->setAllowedTypes(static::DISABLED_VALUE_OPTION, ['null', 'string', 'int', 'array', 'object', 'bool', 'float', 'callback', Closure::class])
            ->setAllowedTypes(static::SWITCH_EVENT_OPTION, ['string', 'null'])
            ->setAllowedTypes(static::SWITCH_STATE_ON_DISABLE_OPTION, 'string')
            ->setAllowedValues(static::SWITCH_STATE_ON_DISABLE_OPTION, ['off', 'on'])
            ->setAllowedTypes(static::SWITCH_LABEL, ['bool', 'string'])
        ;
    }

    /**
     * Go up in the form parents until a collection is detected, if the rendered form name matches the prototype_name
     * config from the parent, it means the prototype is being rendered not an actual form field.
     *
     * @param FormInterface $form
     *
     * @return bool
     */
    private function isRenderingPrototype(FormInterface $form): bool
    {
        $checkedForm = $form;
        while ($checkedForm !== null && $checkedForm->getParent() !== null) {
            $prototypeName = $checkedForm->getParent()->getConfig()->getOption('prototype_name');
            if ($checkedForm->getName() === $prototypeName) {
                return true;
            }

            $checkedForm = $checkedForm->getParent();
        }

        return false;
    }

    /**
     * The switch option may be defined on a compound input, so we need to get back the parent with the option to get
     * back the appropriate options and be able to check the disabled status even for children. For non-compound forms
     * the switchable parent is actually itself.
     *
     * @param FormInterface $form
     *
     * @return FormInterface|null
     */
    private function getSwitchableParent(FormInterface $form): ?FormInterface
    {
        if (!$form->getParent()) {
            return null;
        }

        if ($form->getConfig()->getOption(static::SWITCH_OPTION, false)) {
            return $form;
        }

        return $this->getSwitchableParent($form->getParent());
    }
}
