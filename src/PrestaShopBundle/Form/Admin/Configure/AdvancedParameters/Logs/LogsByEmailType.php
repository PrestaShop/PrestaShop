<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShopBundle\Form\Admin\Type\LogSeverityChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Logs by email" form in Logs page.
 */
final class LogsByEmailType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('logs_by_email', LogSeverityChoiceType::class, [
                'placeholder' => $this->trans(
                    'None',
                    'Admin.Global'
                ),
                'label' => $this->trans(
                    'Minimum severity level',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Click on "None" to disable log alerts by email or enter the recipients of these emails in the following field.',
                    'Admin.Advparameters.Help'
                ),
            ])
            ->add('logs_email_receivers', TextType::class, [
                'label' => $this->trans(
                    'Send emails to',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Log alerts will be sent to these emails. Please use a comma to separate them (e.g. pub@prestashop.com, anonymous@psgdpr.com).',
                    'Admin.Advparameters.Help'
                ),
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
        return 'logs_by_email_block';
    }
}
