<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Gtin;
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
                'label_help_box' => $this->trans('Your own primary unique product code used to identify this product. We recommend using a clear and consistent scheme that helps you keep everything organized. Allowed special characters: %allowed_characters%', 'Admin.Global', ['%allowed_characters%' => '.-_#']),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_REFERENCE),
                    new Length(['max' => Reference::MAX_LENGTH]),
                ],
                'empty_data' => '',
            ])
            ->add('mpn', TextType::class, [
                'required' => false,
                'label' => $this->trans('MPN', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Manufacturer part number that allows to identify this product internationally.', 'Admin.Catalog.Help'),
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
                'label' => $this->trans('GTIN (EAN, JA, ITF or UCC code)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('The product\'s worldwide barcode. Filling this field improves product traceability, catalog management, and overall searchability.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_GTIN),
                    new Length(['max' => Gtin::MAX_LENGTH]),
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
            'label_help_box' => $this->trans('All existing identifiers of the product. We recommend filling in every relevant field you can obtain, as it will improve the product\'s searchability and management.', 'Admin.Catalog.Help'),
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
