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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class QuantityType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $stockManagementEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $stockManagementEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $stockManagementEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->stockManagementEnabled = $stockManagementEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->stockManagementEnabled) {
            $builder
                ->add('quantity', NumberType::class, [
                    'required' => false,
                    'label' => $this->trans('Quantity', 'Admin.Catalog.Feature'),
                    'constraints' => [
                        new NotBlank(),
                        new Type(['type' => 'numeric']),
                    ],
                    'default_empty_data' => 0,
                ])
            ;
        }

        $builder
            ->add('minimal_quantity', NumberType::class, [
                'label' => $this->trans('Minimum quantity for sale', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('The minimum quantity required to buy this product (set to 1 to disable this feature). E.g.: if set to 3, customers will be able to purchase the product only if they take at least 3 in quantity.', 'Admin.Catalog.Help'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                'default_empty_data' => 0,
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
            'label' => $this->trans('Quantities', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
