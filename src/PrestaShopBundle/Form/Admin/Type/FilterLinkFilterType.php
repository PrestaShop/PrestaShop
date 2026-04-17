<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Generic form type for filter link groups.
 * This creates a hidden field that can be controlled by a FilterLinkGroup component.
 */
class FilterLinkFilterType extends AbstractType
{
    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
        $view->vars['filter_options'] = $options['filter_options'] ?? [];
        $view->vars['filter_field_selector'] = $options['filter_field_selector'] ?? null;
        $view->vars['default_value'] = $options['default_value'] ?? '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'required' => false,
            'filter_options' => [],
            'filter_field_selector' => null,
            'default_value' => '',
            'attr' => [
                'class' => 'js-filter-link-field',
            ],
            'row_attr' => [
                'class' => 'd-none',
            ],
        ]);

        $resolver->setDefined([
            'filter_field_name',
        ]);

        $resolver->setAllowedTypes('filter_field_name', 'string');
        $resolver->setAllowedTypes('filter_options', 'array');
        $resolver->setAllowedTypes('filter_field_selector', ['string', 'null']);
        $resolver->setAllowedTypes('default_value', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'filter_link';
    }
}
