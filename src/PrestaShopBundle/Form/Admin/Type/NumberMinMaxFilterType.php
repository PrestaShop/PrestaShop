<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines the number type two inputs of min and max value - designed to fit grid in grid filter.
 */
final class NumberMinMaxFilterType extends AbstractType
{
    use TranslatorAwareTrait;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'min_field_options' => [],
            'max_field_options' => [],
            'constraints' => [
                new Callback([
                    'callback' => function (?array $impactData, ExecutionContextInterface $context) {
                        if (!isset($impactData['min_field']) || !isset($impactData['max_field'])) {
                            return;
                        }

                        if ($impactData['min_field'] > $impactData['max_field']) {
                            $context
                                ->buildViolation($this->trans('Maximum value must be higher than minimum value.', [], 'Admin.Notifications.Warning'))
                                ->addViolation()
                            ;
                        }
                    },
                ]),
            ],
        ]);

        $resolver->setAllowedTypes('min_field_options', 'array');
        $resolver->setAllowedTypes('max_field_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['min_field_options']['attr']['placeholder'])) {
            $options['min_field_options']['attr']['placeholder'] = $this->trans('Min', [], 'Admin.Global');
        }

        if (!isset($options['max_field_options']['attr']['placeholder'])) {
            $options['max_field_options']['attr']['placeholder'] = $this->trans('Max', [], 'Admin.Global');
        }

        $builder->add('min_field', NumberType::class, $options['min_field_options']);
        $builder->add('max_field', NumberType::class, $options['max_field_options']);
    }
}
