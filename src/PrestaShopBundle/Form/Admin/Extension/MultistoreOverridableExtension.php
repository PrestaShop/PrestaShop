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

use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\Pricing\RetailPriceType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class MultistoreOverridableExtension extends AbstractTypeExtension
{
    public const MULTISTORE_OVERRIDE_ALL_PREFIX = 'multistore_override_all_';

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

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
        MultistoreContextCheckerInterface $multistoreContextChecker,
        FormBuilderModifier $formBuilderModifier,
        TranslatorInterface $translator
    ) {
        $this->multistoreContextChecker = $multistoreContextChecker;
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
            // This is the container that includes the overridable fields
            // @todo: this is hardcoded for now but this extension needs to be improved so that the activation
            // of the extension is more automatic, maybe relying on a parent form type like MultistoreConfigurationTypeExtension
            // but it's not ideal
            RetailPriceType::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->multistoreContextChecker->isSingleShopContext()) {
            return;
        }

        /** @var FormBuilderInterface $child */
        foreach ($builder->all() as $childName => $child) {
            $overridable = (bool) $child->getOption('overridable_for_all');
            if (!$overridable) {
                continue;
            }

            // We add the checkbox after even if the form theme will render it before, this prevents its from being
            // automatically rendered by the form_row
            $this->formBuilderModifier->addAfter(
                $builder,
                $childName,
                self::MULTISTORE_OVERRIDE_ALL_PREFIX . $childName,
                CheckboxType::class,
                [
                    'label' => $this->getCheckboxLabel(),
                    'attr' => [
                        'data-value-type' => 'boolean',
                    ],
                ]
            );
        }
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$this->multistoreContextChecker->isSingleShopContext()) {
            return;
        }

        $view->vars = array_replace($view->vars, [
            'overridable_for_all' => $options['overridable_for_all'],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'overridable_for_all' => false,
        ]);
    }

    /**
     * @return string
     */
    private function getCheckboxLabel(): string
    {
        if (!$this->checkboxLabel) {
            $this->checkboxLabel = $this->translator->trans('Apply the changes in all the shops', [], 'Admin.Global');
        }

        return $this->checkboxLabel;
    }
}
