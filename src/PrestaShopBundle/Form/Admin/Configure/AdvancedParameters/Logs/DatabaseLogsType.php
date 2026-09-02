<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShopBundle\Form\Admin\Type\LogSeverityChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Database" form in Logs page.
 */
final class DatabaseLogsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('database_min_logger_level', LogSeverityChoiceType::class, [
                'required' => true,
                'label' => $this->trans(
                    'Minimum severity level',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'Indicate the minimum log levels that will be saved in database.',
                    'Admin.Advparameters.Help'
                ),
            ])
        ;
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
        return 'database_logs_block';
    }
}
