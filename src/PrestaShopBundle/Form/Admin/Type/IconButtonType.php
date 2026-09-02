<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A form button with material icon.
 */
class IconButtonType extends ButtonType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'icon' => null,
                'type' => 'button',
            ])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', ['button', 'link'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['button_icon'] = $options['icon'];
        $view->vars['button_type'] = $options['type'];
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): ?string
    {
        return ButtonType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'icon_button';
    }
}
