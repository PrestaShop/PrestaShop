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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateRangeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', DatePickerType::class, $this->getFromFieldOptions($options))
            ->add('to', DatePickerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'To',
                    'class' => 'to',
                ],
                'translation_domain' => 'Admin.Global',
                'date_format' => $options['date_format'],
            ]);
        if ($options['has_unlimited_checkbox']) {
            $builder->add('unlimited', CheckboxType::class, [
                'required' => false,
                'translation_domain' => 'Admin.Global',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'date_format' => 'YYYY-MM-DD',
            'start_date' => false,
            'has_unlimited_checkbox' => false,
        ]);
        $resolver->setAllowedTypes('date_format', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'date_range';
    }

    /**
     * @param array $options
     * @return array
     */
    private function getFromFieldOptions(array $options): array
    {
        $fromFieldOptions = [
            'required' => false,
            'attr' => [
                'placeholder' => 'From',
                'class' => 'from',
            ],
            'translation_domain' => 'Admin.Global',
            'date_format' => $options['date_format'],
        ];

        if ($options['start_date']) {
            $fromFieldOptions['data'] = $options['start_date'];
        }

        return $fromFieldOptions;
    }
}
