<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShopBundle\Form\Admin\Type\LogSeverityChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

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
            ->add('database_duplicate_check_limit', IntegerType::class, [
                'required' => true,
                'label' => $this->trans(
                    'Duplicate check range',
                    'Admin.Advparameters.Feature'
                ),
                'help' => $this->trans(
                    'A log is not saved again when the same one is found among this number of most recent logs. With 0, only the logs of the same request are compared.',
                    'Admin.Advparameters.Help'
                ),
                'unit' => $this->trans('logs', 'Admin.Advparameters.Feature'),
                'attr' => ['min' => 0],
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
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
