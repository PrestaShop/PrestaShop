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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

class HeaderType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $stockManagementEnabled;

    /**
     * @var bool
     */
    private $isEcotaxEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $stockManagementEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $stockManagementEnabled,
        bool $isEcotaxEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->stockManagementEnabled = $stockManagementEnabled;
        $this->isEcotaxEnabled = $isEcotaxEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cover_thumbnail', ImagePreviewType::class, [
                'label' => false,
            ])
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Product name', 'Admin.Catalog.Feature'),
                'type' => TextType::class,
                'constraints' => $options['active'] ? [new DefaultLanguage()] : [],
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_CATALOG_NAME),
                        new Length(['max' => ProductSettings::MAX_NAME_LENGTH]),
                    ],
                    'attr' => [
                        'class' => 'serp-default-title',
                    ],
                ],
                'row_attr' => [
                    'class' => 'header-name',
                ],
                'modify_all_shops' => true,
            ])
            ->add('type', ProductTypeType::class, [
                'attr' => [
                    'data-switch-modal-title' => $this->trans('Change the product type', 'Admin.Catalog.Notification'),
                    'data-modal-title' => $this->trans('Are you sure you want to change the product type?', 'Admin.Catalog.Notification'),
                    'data-modal-apply' => $this->trans('Change product type', 'Admin.Catalog.Notification'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Global'),
                    'data-confirm-message' => $this->trans('Changing the product type will immediately save the product and refresh the page.', 'Admin.Catalog.Notification'),
                    'data-combinations-warning' => $this->trans('This will delete all combinations.', 'Admin.Catalog.Notification'),
                    'data-pack-warning' => $this->trans('This will delete the list of products in this pack.', 'Admin.Catalog.Notification'),
                    'data-virtual-warning' => $this->trans('This will delete the associated virtual file.', 'Admin.Catalog.Notification'),
                    'data-ecotax-warning' => $this->trans('This will reset the ecotax value and may impact your retail price (tax incl.).', 'Admin.Catalog.Notification'),
                    'data-stock-warning' => $this->trans('This will reset the stock of this product.', 'Admin.Catalog.Notification'),
                    'data-stock-enabled' => $this->stockManagementEnabled,
                    'data-ecotax-enabled' => $this->isEcotaxEnabled,
                    'class' => 'header-product-type-selector',
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => false,
                'choices' => [
                    $this->trans('Offline', 'Admin.Global') => false,
                    $this->trans('Online', 'Admin.Global') => true,
                ],
            ])
            ->add('initial_type', HiddenType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'active' => false,
                'required' => false,
                'label' => false,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/header.html.twig',
            ])
            ->setAllowedTypes('active', ['bool'])
        ;
    }
}
