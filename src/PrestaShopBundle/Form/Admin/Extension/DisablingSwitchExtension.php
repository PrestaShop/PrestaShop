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

class DisablingSwitchExtension extends AbstractTypeExtension
{
    public const FIELD_PREFIX = 'disabling_switch_';

    public const SWITCH_OPTION = 'disabling_switch';
    public const DISABLED_VALUE_OPTION = 'disabled_value';
    public const DISABLED_DATA_OPTION = 'disabled_data';

    /**
     * @var EventSubscriberInterface
     */
    private $addDisablingSwitchListener;

    /**
     * @var EventSubscriberInterface
     */
    private $adaptDisableStateListener;

    public function __construct(
        EventSubscriberInterface $addDisablingSwitchListener,
        EventSubscriberInterface $adaptDisableStateListener
    ) {
        $this->addDisablingSwitchListener = $addDisablingSwitchListener;
        $this->adaptDisableStateListener = $adaptDisableStateListener;
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
        // The children state will be adapted from the parent form, but we have no way to detect the form parent from
        // the builder so the AdaptDisableStateListener listener is added on any compound form since it may potentially
        // contain some DisablingSwitchType, the listener will then detect if some disabling fields are present or not
        if ($builder->getCompound()) {
            // $builder->addEventSubscriber($this->adaptDisableStateListener);
        }

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
                // We use this value to know if the field state is disabled or not on first rendering
                //
                // When left null the default_empty_data option will be used for comparison, if default_empty_data
                // is also null then the empty_data option is used
                //
                // You can also specify a callback or a closure for this option this allows more complex use case
                // to define if the form is considered empty or not (useful for compound form based on multiple values).
                // The callback will receive the form data and the FormInterface, it must return true if the field should
                // be disabled
                //
                // ex: 'disabled_value' => function (?array $data, FormInterface $form): bool {
                //          return empty($data['reduction_type']) || empty($data['reduction_value']);
                //      },
                self::DISABLED_VALUE_OPTION => null,
            ])
            ->setAllowedTypes(self::SWITCH_OPTION, 'bool')
            ->setAllowedTypes(self::DISABLED_VALUE_OPTION, ['null', 'string', 'int', 'array', 'object', 'bool', 'float', 'callback', Closure::class])
            ->setDefined(self::DISABLED_DATA_OPTION)
            ->setAllowedTypes(self::DISABLED_DATA_OPTION, ['null', 'string', 'int', 'array', 'object', 'bool', 'float', 'callback', Closure::class])
        ;
    }
}
