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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Ean13;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Isbn;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Upc;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ReferencesType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class, [
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'label_help_box' => $this->trans('Allowed special characters: %allowed_characters%', 'Admin.Global', ['%allowed_characters%' => '.-_#']),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_REFERENCE),
                    new Length(['max' => Reference::MAX_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('mpn', TextType::class, [
                'required' => false,
                'label' => $this->trans('MPN', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('MPN is used internationally to identify the Manufacturer Part Number.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new Length(['max' => ProductSettings::MAX_MPN_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('upc', TextType::class, [
                'required' => false,
                'label' => $this->trans('UPC barcode', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_UPC),
                    new Length(['max' => Upc::MAX_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('ean_13', TextType::class, [
                'required' => false,
                'label' => $this->trans('EAN-13 or JAN barcode', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_EAN_13),
                    new Length(['max' => Ean13::MAX_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => $this->trans('ISBN', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('ISBN is used internationally to identify books and their various editions.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_ISBN),
                    new Length(['max' => Isbn::MAX_LENGTH]),
                ],
                'empty_data' => '',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('References', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
