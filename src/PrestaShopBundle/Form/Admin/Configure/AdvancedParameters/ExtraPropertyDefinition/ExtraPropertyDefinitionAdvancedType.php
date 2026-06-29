<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * "Advanced form integration" card: how the field renders in BO forms/grids beyond the
 * default mapping.
 */
class ExtraPropertyDefinitionAdvancedType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form_field_type', TextType::class, [
                'label' => $this->trans('Symfony form type', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully-qualified Symfony form type class name to override the default type mapping (e.g. Symfony\Component\Form\Extension\Core\Type\UrlType).', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('form_options', TextareaType::class, [
                'label' => $this->trans('Form options (JSON)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Extra options merged into the Symfony form type constructor, as a JSON object. Example: {"attr": {"class": "my-class"}, "row_attr": {"data-toggle": "tooltip"}}', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('associated_forms', TextareaType::class, [
                'label' => $this->trans('Associated forms (JSON)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('JSON array of form placement entries. Each entry: "formId", "formId:path", "formId:path:before", or "formId:path:after". Example: ["product_combination:combination_details.reference:after"].', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('associated_grids', TextareaType::class, [
                'label' => $this->trans('Associated grids (JSON)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('JSON array of grid placement entries. Each entry: "gridId", "gridId:columnId", "gridId:columnId:before", or "gridId:columnId:after". Example: ["product:reference:after"].', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('associated_apis', TextareaType::class, [
                'label' => $this->trans('Associated APIs (JSON)', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('JSON array of Admin API placement entries. Each entry is an operation URI template with optional methods. Example: ["/products", "/products/{productId}:GET,PATCH"].', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
            ]);
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
