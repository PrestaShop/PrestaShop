<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AmountCurrencyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $amountOptions = [
            'constraints' => $options['amount_constraints'],
        ];

        if (count($options['currencies']) > 1) {
            $builder
                ->add('amount', NumberType::class, $amountOptions)
                ->add('id_currency', ChoiceType::class, [
                    'choices' => $options['currencies'],
                ])
            ;
        } else {
            $firstCurrencyKey = array_keys($options['currencies'])[0];
            $amountOptions['unit'] = $firstCurrencyKey;
            $builder
                ->add('amount', NumberType::class, $amountOptions)
                ->add('id_currency', HiddenType::class, [
                    'data' => $options['currencies'][$firstCurrencyKey],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('amount_constraints', []);
        $resolver->setDefault('label', false);
        $resolver->setDefault('inherit_data', true);
        $resolver->setRequired('currencies');
        $resolver->setAllowedTypes('currencies', ['array']);
    }

    public function getBlockPrefix(): string
    {
        return 'amount_currency';
    }
}
