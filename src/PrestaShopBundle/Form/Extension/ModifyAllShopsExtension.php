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

namespace PrestaShopBundle\Form\Extension;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
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

/**
 * This extension adds a new modify_all_shops option for form fields, then when the
 * form is built a checkbox is automatically added which matches the configured field.
 * This checkbox can then be used to apply the modification from the field to all shops.
 */
class ModifyAllShopsExtension extends AbstractTypeExtension
{
    public const MODIFY_ALL_SHOPS_PREFIX = 'modify_all_shops_';

    /**
     * @var FeatureInterface
     */
    private $multiStoreFeature;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $checkboxLabel;

    public function __construct(
        FeatureInterface $multiStoreFeature,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        TranslatorInterface $translator
    ) {
        $this->multiStoreFeature = $multiStoreFeature;
        $this->multistoreContextChecker = $multistoreContextChecker;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->multiStoreFeature->isUsed() || !$this->multistoreContextChecker->isSingleShopContext()) {
            return;
        }

        $isOverridden = $builder->getOption('modify_all_shops');
        if ($isOverridden) {
            $label = $this->getCheckboxLabel();
            // We want to add a field on the parent, sadly builder doesn't allow to get the parent builder, so we can't modify the parent builder
            // Another solution would be to loop through a builder's children, but they are not always present when the extension is run (mainly
            // because the root element is first built, and the compound children are built just in time later) so when the extension first runs
            // the children are not accessible nor parseable
            // For this reason the only moment where children are all present is when the form is already completely built, which is why we have
            // to add the new fields directly on the built form, so we need to rely on form events to handle this
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($label) {
                $form = $event->getForm();
                $parent = $form->getParent();
                $parent->add(self::MODIFY_ALL_SHOPS_PREFIX . $form->getName(),
                    CheckboxType::class,
                    [
                        'label' => $label,
                        'attr' => [
                            'container_class' => 'modify-all-shops',
                            'data-value-type' => 'boolean',
                        ],
                    ]
                );
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
        if (!$this->multiStoreFeature->isUsed() || !$this->multistoreContextChecker->isSingleShopContext()) {
            return;
        }

        $view->vars = array_replace($view->vars, [
            'modify_all_shops' => $options['modify_all_shops'],
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'modify_all_shops' => false,
        ]);
    }

    /**
     * @return string
     */
    private function getCheckboxLabel(): string
    {
        if (!$this->checkboxLabel) {
            $this->checkboxLabel = $this->translator->trans('Apply changes to all stores', [], 'Admin.Global');
        }

        return $this->checkboxLabel;
    }
}
