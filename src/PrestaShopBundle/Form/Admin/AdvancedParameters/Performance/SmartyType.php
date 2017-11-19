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
namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class generates the "Smarty" form in Performance page
 */
class SmartyType extends CommonAbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template_compilation', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    0 => 'Never recompile template files',
                    1 => 'Recompile templates if the files have been updated',
                    2 => 'Force compilation'
                ),
                'choices_as_values' => false,
                'required' => true,
            ))
            ->add('cache', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    false => 'No',
                    true => 'Yes',
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ))
            ->add('multi_front_optimization', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    false => 'No',
                    true => 'Yes',
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ))
            ->add('caching_type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'filesystem' => 'File System',
                    'mysql' => 'MySQL',
                ),
                'choices_as_values' => false,
                'required' => true,
            ))
            ->add('clear_cache', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'never' => 'Never clear cache files',
                    'everytime' => 'Clear cache everytime something has been modified',
                ),
                'choices_as_values' => false,
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
        return 'performance_smarty_block';
    }
}
