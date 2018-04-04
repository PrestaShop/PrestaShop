<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Logs;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "filters" form in Logs page, on "Logs" table header.
 */
final class FilterLogsByAttributeType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_log', TextType::class, array('required' => false))
            ->add('employee', TextType::class, array('required' => false))
            ->add('severity', TextType::class, array('required' => false))
            ->add('message', TextType::class, array('required' => false))
            ->add('object_type', TextType::class, array('required' => false))
            ->add('object_id', TextType::class, array('required' => false))
            ->add('error_code', TextType::class, array('required' => false))
            ->add('date_from', DatePickerType::class, array(
                'required' => false,
                'attr' => array('placeholder' => 'From'),
                'translation_domain' => 'Admin.Global',
            ))
            ->add('date_to', DatePickerType::class, array(
                'required' => false,
                'attr' => array('placeholder' => 'To'),
                'translation_domain' => 'Admin.Global',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'Admin.Advparameters.Feature'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'logs_filters_block';
    }
}