<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidExtraPropertyAssociations;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ExtraPropertyCatalogPresenter;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Advanced form integration" card: how the field renders in BO forms/grids beyond the
 * default mapping.
 *
 * The card exposes the forms/grids/APIs catalogs and the type=>default form type map as the
 * "extra_property_catalogs" view var, inlined by the form theme as a JSON block so the picker
 * components can suggest ids without AJAX (the per-form field tree stays lazy — see
 * ExtraPropertyDefinitionController::formFieldsAction()).
 */
class ExtraPropertyDefinitionAdvancedType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ExtraPropertyCatalogPresenter $catalogPresenter,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form_type', TextType::class, [
                'label' => $this->trans('Symfony form type', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully-qualified Symfony form type class name to override the default type mapping (e.g. Symfony\Component\Form\Extension\Core\Type\UrlType).', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('form_options', TextareaType::class, [
                'label' => $this->trans('Form options (JSON)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Extra options merged into the Symfony form type constructor, as a JSON object. Example: {"attr": {"class": "my-class"}, "row_attr": {"data-toggle": "tooltip"}}', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
                'constraints' => [
                    new Json(['message' => $this->trans('The form options must be a valid JSON object.', 'Admin.Advparameters.Notification')]),
                ],
            ])
            ->add('associated_forms', TextareaType::class, [
                'label' => $this->trans('Associated forms', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('One form placement entry per line: "formId", "formId:path", or "formId:path:before|after" (nested path segments separated by dots). Example: product_combination:combination_details.reference:after', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
                'constraints' => [
                    new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_FORM]),
                ],
            ])
            ->add('associated_grids', TextareaType::class, [
                'label' => $this->trans('Associated grids', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('One grid placement entry per line: "gridId", "gridId:columnId", or "gridId:columnId:before|after". Example: product:reference:after', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
                'constraints' => [
                    new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_GRID]),
                ],
            ])
            ->add('associated_apis', TextareaType::class, [
                'label' => $this->trans('Associated APIs', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('One Admin API placement entry per line: an operation URI template, optionally followed by ":" and comma-separated HTTP methods. Examples: /products or /products/{productId}:GET,PATCH', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
                'constraints' => [
                    new ValidExtraPropertyAssociations(['type' => ValidExtraPropertyAssociations::TYPE_API]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['extra_property_catalogs'] = $this->catalogPresenter->presentAdvancedCard();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Advanced form integration', 'Admin.Advparameters.Feature'),
            'icon' => 'build',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CardType::class;
    }
}
