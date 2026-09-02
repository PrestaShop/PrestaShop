<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This type is used by the DisablingExtension and automatically added on form fields which have
 * the disabling_switcher option enabled.
 *
 * @todo: this type doesn't seem to work on its own (e.g. when trying $builder->add('foo', DisablingSwitchType))
 */
class DisablingSwitchType extends SwitchType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            // The set default options from parent type
            ->setDefaults([
                'target_selector' => '',
                'disable_on_match' => true,
                'show_choices' => false,
                'label' => false,
                'required' => false,
                'label_attr' => [
                    'class' => 'font-weight-normal small mb-0',
                ],
                'row_attr' => [
                    'class' => 'ps-disabling-switch',
                ],
                'switch_event' => null,
            ])
            ->setAllowedTypes('disable_on_match', 'bool')
            ->setAllowedTypes('target_selector', 'string')
            ->setAllowedTypes('switch_event', ['string', 'null'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['attr']['data-target-selector'] = $options['target_selector'];
        $view->vars['attr']['data-matching-value'] = '0';
        $view->vars['attr']['data-disable-on-match'] = (int) $options['disable_on_match'];

        // Optional event to trigger on switch
        if (!empty($options['switch_event'])) {
            $view->vars['attr']['data-switch-event'] = $options['switch_event'];
        }
    }

    public function getParent(): string
    {
        return SwitchType::class;
    }
}
