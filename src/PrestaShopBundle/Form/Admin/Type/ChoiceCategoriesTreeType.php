<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to create a category selector using Nested sets.
 */
class ChoiceCategoriesTreeType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     *
     * Add the var choices to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices'] = $options['list'];
        $view->vars['multiple'] = $options['multiple'];
        $view->vars['expanded'] = $options['expanded'];

        // if form is submitted, inject categories values array to check or not each field
        if (!empty($view->vars['value']) && !empty($view->vars['value']['tree'])) {
            $view->vars['submitted_values'] = array_flip($view->vars['value']['tree']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tree', ChoiceType::class, [
            'label' => false,
            'choices' => $options['valid_list'],
            'required' => false,
            'multiple' => true,
            'expanded' => true,
            'error_bubbling' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => '',
            'list' => [],
            'valid_list' => [],
            'multiple' => true,
            'expanded' => true,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'choice_tree';
    }
}
