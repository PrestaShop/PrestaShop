<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used as a container of sub forms, each sub form will be rendered as a part of an accordion.
 */
class AccordionType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['expand_first'] = $options['expand_first'];
        $view->vars['expand_all'] = $options['expand_all'];
        $view->vars['expand_on_error'] = $options['expand_on_error'];
        $view->vars['display_one'] = $options['display_one'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expand_first' => true,
            'expand_all' => false,
            'expand_on_error' => true,
            'display_one' => true,
        ]);
    }
}
