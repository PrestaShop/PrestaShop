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
namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;

class CustomMoneyType extends AbstractTypeExtension implements DataTransformerInterface
{
    const PRESTASHOP_DECIMALS = 6;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\MoneyType';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'precision' => null,
            'scale' => self::PRESTASHOP_DECIMALS,
            'grouping' => false,
            'divisor' => 1,
            'currency' => 'EUR',
            'compound' => false,
        ));

        $resolver->setAllowedTypes('scale', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function transform($data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        return empty($data) ? '' : $data;
    }

    /**
     * Partial correction for "Price Tax Calculation" on Product view (only on arab).
     * When your local is in ar-SA, we use the Eastearn Arabic number (٠١٢٣٤٥٧٨٩).
     * This number is translate in a string data and not retransform after in Weastern Arabic number.
     * In this tempory fix, we forced to use the Westearn Arab number like in 1.6.
     * When we use this, the tax calculation is correct.
     */
    public function __construct() {
        if ('ar' === substr(\Locale::getDefault(), 0, 2)) {
            \Locale::setDefault('ar-TN');
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function __destruct() {
        if ('ar' ===  substr(\Locale::getDefault(), 0, 2)) {
            \Locale::setDefault('ar-SA');
        }
    }
}
