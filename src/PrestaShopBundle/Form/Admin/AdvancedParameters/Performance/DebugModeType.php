<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Debug mode" form in Performance page
 */
class DebugModeType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disable_non_native_modules', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ))
            ->add('disable_overrides', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ))
            ->add('debug_mode', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
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
        return 'performance_debug_mode_block';
    }
}
