<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is basically a choice types, but it offers a more enriched UX
 * instead of relying on radio buttons each choice is displayed with a div block
 * in which you can specify more details that the option name:
 *   - add help message for more details about the choice
 *   - add icon on each choice
 *
 * Note: so far only tested with radio buttons (expanded: true, multiple: false), other
 * configurations will likely need appropriate improvements at least in the PrestaShop
 * UI kit form theme.
 */
class EnrichedChoiceType extends ChoiceType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['flex_direction'] = $options['flex_direction'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/enriched_choice.html.twig',
            'flex_direction' => 'column',
        ]);
        $resolver->setAllowedValues('flex_direction', ['column', 'row']);
    }

    public function getBlockPrefix(): string
    {
        return 'enriched_choice';
    }
}
