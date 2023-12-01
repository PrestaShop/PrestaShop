<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AmountCurrencyType
 * 
 * Form type documentation: 
 * https://devdocs.prestashop-project.org/8/development/components/form/types-reference/amount-currency/
 */
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
