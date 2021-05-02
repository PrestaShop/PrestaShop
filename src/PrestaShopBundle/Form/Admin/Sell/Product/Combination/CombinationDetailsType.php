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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class CombinationDetailsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => $this->trans('ISBN', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_ISBN),
                ],
                'empty_data' => '',
            ])
            ->add('ean_13', TextType::class, [
                'required' => false,
                'label' => $this->trans('EAN-13 or JAN barcode', 'Admin.Catalog.Feature'),
                'help' => $this->trans('This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_EAN_13),
                ],
                'empty_data' => '',
            ])
            ->add('upc', TextType::class, [
                'required' => false,
                'label' => $this->trans('UPC barcode', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_UPC),
                ],
                'empty_data' => '',
            ])
            ->add('mpn', TextType::class, [
                'required' => false,
                'label' => $this->trans('MPN', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Length(['max' => ProductSettings::MAX_MPN_LENGTH]),
                ],
                'empty_data' => '',
            ])
        ;
    }
}
