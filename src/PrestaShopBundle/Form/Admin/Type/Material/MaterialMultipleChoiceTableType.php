<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type\Material;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialMultipleChoiceTableType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['multiple_choices'] as $choices) {
            $builder->add($choices['name'], ChoiceType::class, [
                'label' => $choices['label'],
                'choices' => $choices['choices'],
                'expanded' => true,
                'multiple' => $choices['multiple'],
                'choice_label' => false,
                'choice_translation_domain' => false,
            ]);

            $builder->get($choices['name'])->addModelTransformer(new CallbackTransformer(
                function ($value) use ($choices) {
                    if (is_array($value) && false === $choices['multiple']) {
                        return reset($value);
                    }

                    return $value;
                },
                function ($value) {
                    return $value;
                }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices'] = $options['choices'];
        $view->vars['scrollable'] = $options['scrollable'];
        $view->vars['headers_to_disable'] = $options['headers_to_disable'];
        $view->vars['headers_fixed'] = $options['headers_fixed'];
        $view->vars['table_label'] = $options['table_label'];
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $entryIndexMapping = [];

        foreach ($view->children as $childChoiceName => $childChoiceView) {
            foreach ($childChoiceView->children as $index => $childChoiceEntryView) {
                $entryIndexMapping[$childChoiceEntryView->vars['value']][$childChoiceName] = $index;
            }
        }

        $view->vars['child_choice_entry_index_mapping'] = $entryIndexMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'multiple_choices',
                'choices',
                // in some cases we want to disable
                // header for columns
                'headers_to_disable',
            ])
            ->setDefaults([
                'scrollable' => true,
                'headers_to_disable' => [],
                'headers_fixed' => false,
                'table_label' => false,
            ])
        ;

        $resolver->setAllowedTypes('choices', 'array');
        $resolver->setAllowedTypes('multiple_choices', 'array');
        $resolver->setAllowedTypes('scrollable', 'bool');
        $resolver->setAllowedTypes('headers_to_disable', 'array');
        $resolver->setAllowedTypes('headers_fixed', 'bool');
        $resolver->setAllowedTypes('table_label', ['bool', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'material_multiple_choice_table';
    }
}
