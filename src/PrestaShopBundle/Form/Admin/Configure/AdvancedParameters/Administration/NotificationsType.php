<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('show_notifs_new_orders', SwitchType::class, [
                'label' => $this->trans('Show notifications for new orders', 'Admin.Advparameters.Feature'),
            ])
            ->add('show_notifs_new_customers', SwitchType::class, [
                'label' => $this->trans('Show notifications for new customers', 'Admin.Advparameters.Feature'),
            ])
            ->add('show_notifs_new_messages', SwitchType::class, [
                'label' => $this->trans('Show notifications for new messages', 'Admin.Advparameters.Feature'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'administration_notification_block';
    }
}
