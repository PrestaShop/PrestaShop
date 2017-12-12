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
namespace PrestaShopBundle\Form\Admin\ShopParameters\General;

use PrestaShopBundle\Form\Admin\Type\IpAddressType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Validator\Constraints\TinyMceMaxLength;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance
 */
class MaintenanceType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('disable_shop', ChoiceType::class, array(
                'choices_as_values' => true,
                'choices'  => array(
                    'No' => false,
                    'Yes' => true,
                ),
                'choice_translation_domain' => 'Admin.Global',
                'required' => true,
            ))
            ->add('maintenance_ip', IpAddressType::class, array(
                'required' => true,
            ))
            ->add('maintenance_text', TranslateType::class, array(
                'type' => TextareaType::class,
                'options' => array(
                    'attr' => array(
                        'class' => 'autoload_rte',
                        'counter' => 21844
                    ),
                    'constraints' => array(
                        new TinyMceMaxLength(array(
                            'max' => 21844
                        ))
                    ),
                    'required' => false,
                ),
                'locales' => $this->locales,
                'hideTabs' => false,
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
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'maintenance_general_block';
    }
}
