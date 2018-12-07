<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * This form class is responsible to generate the form for bulk combination feature
 * Note this form is not validated from the server side.
 */
class ProductCombinationBulk extends CommonAbstractType
{
    private $isoCode;
    private $priceDisplayPrecision;
    private $translator;
    private $configuration;

    public function __construct(TranslatorInterface $translator, Configuration $configuration)
    {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $is_stock_management = $this->configuration->get('PS_STOCK_MANAGEMENT');
        $this->isoCode = $options['iso_code'];
        $this->priceDisplayPrecision = $options['price_display_precision'];

        if ($is_stock_management) {
            $builder->add('quantity', NumberType::class, array(
                'required' => true,
                'label' => $this->translator->trans('Quantity', array(), 'Admin.Catalog.Feature'),
            ));
        }

        $builder->add('cost_price', MoneyType::class, array(
            'required' => false,
            'label' => $this->translator->trans('Cost Price', array(), 'Admin.Catalog.Feature'),
            'attr' => array('data-display-price-precision' => self::PRESTASHOP_DECIMALS),
            'currency' => $this->isoCode,
        ))
            ->add('impact_on_weight', NumberType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Impact on weight', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('impact_on_price_te', MoneyType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Impact on price (tax excl.)', array(), 'Admin.Catalog.Feature'),
                'currency' => $this->isoCode,
            ))
            ->add('impact_on_price_ti', MoneyType::class, array(
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('Impact on price (tax incl.)', array(), 'Admin.Catalog.Feature'),
                'currency' => $this->isoCode,
            ))
            ->add('date_availability', DatePickerType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Availability date', array(), 'Admin.Catalog.Feature'),
                'attr' => array('class' => 'date', 'placeholder' => 'YYYY-MM-DD'),
            ))
            ->add('reference', TextType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Reference', array(), 'Admin.Catalog.Feature'),
                'empty_data' => '',
            ))
            ->add('minimal_quantity', NumberType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Minimum quantity', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('low_stock_threshold', NumberType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Low stock level', array(), 'Admin.Catalog.Feature'),
            ))
            ->add('low_stock_alert', CheckboxType::class, array(
                'required' => false,
                'label' => $this->translator->trans('Send me an email when the quantity is below or equals this level', array(), 'Admin.Catalog.Feature'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => false,
            'iso_code' => '',
            'price_display_precision' => '',
        ));
    }

    public function getBlockPrefix()
    {
        return 'product_combination_bulk';
    }
}
