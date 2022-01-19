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

use PrestaShopBundle\Form\FormBuilderModifier;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DisablingToggleExtension extends AbstractTypeExtension
{
    public const FIELD_PREFIX = 'disabling_toggle_';

    /**
     * @var FormBuilderModifier
     */
    private $formBuilderModifier;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $checkboxLabel;

    public function __construct(
        FormBuilderModifier $formBuilderModifier,
        TranslatorInterface $translator
    ) {
        $this->formBuilderModifier = $formBuilderModifier;
        $this->translator = $translator;
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
        //@todo: needs cleaning.
        $isOverridden = $builder->getOption('disabling_toggle');
        if ($isOverridden) {
            $label = $this->getCheckboxLabel();
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($label) {
                $form = $event->getForm();
                $parent = $form->getParent();
                $fieldName = self::FIELD_PREFIX . $form->getName();
                if ($parent->has($fieldName)) {
                    return;
                }
                $parent->add($fieldName, CheckboxType::class, [
                    'label' => $label,
                    'attr' => [
                        'container_class' => 'disabling-toggle',
                        'data-value-type' => 'boolean',
                    ],
                ]);

                $shouldBeDisabled = !$parent->get($fieldName)->getData();

                $formCloner = new FormCloner();
                $newOptions = ['disabled' => $shouldBeDisabled];

                foreach ($form->all() as $childForm) {
                    if ($childForm->getConfig()->hasOption('disabled') && $shouldBeDisabled === $childForm->getConfig()->getOption('disabled')) {
                        continue;
                    }
                    $newChildForm = $formCloner->cloneForm($childForm, array_merge($childForm->getConfig()->getOptions(), $newOptions));
                    $form->add($newChildForm);
                }

                //@todo; need configurable (e.g. it should be possible to change if input is disabled when checkbox is checked or when unchecked
                $newForm = $formCloner->cloneForm($form, array_merge($form->getConfig()->getOptions(), $newOptions));
                $parent->add($newForm);
            });
        }
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'disabling_toggle' => $options['disabling_toggle'],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'disabling_toggle' => false,
        ]);
    }

    /**
     * @return string
     */
    private function getCheckboxLabel(): string
    {
        if (!$this->checkboxLabel) {
            //@todo: need wording check. This toggle should appear next to field, which would be enabled/disabled based on this switch value.
            $this->checkboxLabel = $this->translator->trans('Use field', [], 'Admin.Global');
        }

        return $this->checkboxLabel;
    }
}
