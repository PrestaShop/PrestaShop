<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;

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

        if($is_stock_management){
            $builder->add('quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => true,
                'label' => $this->translator->trans('Quantity', [], 'Admin.Catalog.Feature'),
            ));
        }

        $builder->add('cost_price', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
            'required' => false,
            'label' => $this->translator->trans('Cost Price', [], 'Admin.Catalog.Feature'),
            'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
            'currency' => $this->isoCode,
        ))
            ->add('impact_on_weight', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'label' => $this->translator->trans('Impact on weight', [], 'Admin.Catalog.Feature'),
            ))
            ->add('impact_on_price_te', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
                'required' => false,
                'label' => $this->translator->trans('Impact on price (tax excl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->isoCode,
            ))
            ->add('impact_on_price_ti', 'Symfony\Component\Form\Extension\Core\Type\MoneyType', array(
                'required' => false,
                'mapped' => false,
                'label' => $this->translator->trans('Impact on price (tax incl.)', [], 'Admin.Catalog.Feature'),
                'currency' => $this->isoCode,
            ))
            ->add('date_availability', 'PrestaShopBundle\Form\Admin\Type\DatePickerType', array(
                'required' => false,
                'label' => $this->translator->trans('Availability date', [], 'Admin.Catalog.Feature'),
                'attr' => ['class' => 'date', 'placeholder' => 'YYYY-MM-DD'],
            ))
            ->add('reference', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => $this->translator->trans('Reference', [], 'Admin.Catalog.Feature'),
            ))
            ->add('minimal_quantity', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => false,
                'label' => $this->translator->trans('Minimum quantity', [], 'Admin.Catalog.Feature'),
            ));


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
