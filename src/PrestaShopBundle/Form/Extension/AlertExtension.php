<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds the "alert_message" option to all Form Types.
 *
 * You can use it together with the UI kit form theme to add alter message to your field
 */
class AlertExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'alert_message' => null,
                'alert_type' => 'info',
                'alert_position' => 'append',
                'alert_title' => null,
            ])
            ->setAllowedTypes('alert_message', ['null', 'string', 'array'])
            ->setAllowedTypes('alert_title', ['string', 'null'])
            ->setAllowedTypes('alert_type', ['string'])
            ->setAllowedTypes('alert_position', ['string'])
            ->setAllowedValues('alert_position', ['append', 'prepend'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!empty($options['alert_message'])) {
            $view->vars['alert_message'] = is_string($options['alert_message']) ? [$options['alert_message']] : $options['alert_message'];
            $view->vars['alert_type'] = $options['alert_type'];
            $view->vars['alert_position'] = $options['alert_position'];

            if (is_string($options['alert_title'])) {
                $view->vars['alert_title'] = $options['alert_title'];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
