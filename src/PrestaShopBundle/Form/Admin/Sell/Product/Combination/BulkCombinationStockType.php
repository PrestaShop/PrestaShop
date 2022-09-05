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

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\DeltaQuantityType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class BulkCombinationStockType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var bool
     */
    private $stockManagementEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param bool $stockManagementEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        bool $stockManagementEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->stockManagementEnabled = $stockManagementEnabled;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->stockManagementEnabled) {
            $builder
                ->add('delta_quantity', DeltaQuantityType::class, [
                    'required' => false,
                    'label' => $this->trans('Edit quantity', 'Admin.Catalog.Feature'),
                    'disabling_switch' => true,
                    'disabling_switch_event' => 'combinationSwitchDeltaQuantity',
                    'disabled_value' => function (?array $data) {
                        return empty($data['quantity']) && empty($data['delta']);
                    },
                ])
                ->add('fixed_quantity', IntegerType::class, [
                    'required' => false,
                    'label' => $this->trans('Edit fixed quantity', 'Admin.Catalog.Feature'),
                    'default_empty_data' => 0,
                    'disabling_switch' => true,
                    'disabling_switch_event' => 'combinationSwitchFixedQuantity',
                    'modify_all_shops' => true,
                ])
            ;
        }

        $builder
            ->add('minimal_quantity', NumberType::class, [
                'label' => $this->trans('Minimum order quantity', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                'default_empty_data' => 0,
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('stock_location', TextType::class, [
                'label' => $this->trans('Stock location', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Enter stock location', 'Admin.Catalog.Feature'),
                ],
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('low_stock_threshold', NumberType::class, [
                'label' => $this->trans('Low stock level', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
                // These two options allow to have a default data equals to zero but displayed as empty string
                'default_empty_data' => 0,
                'empty_view_data' => null,
                'modify_all_shops' => true,
                'disabling_switch' => true,
            ])
            ->add('low_stock_alert', SwitchType::class, [
                'required' => false,
                'label' => $this->trans(
                    'Receive a low stock alert by email',
                    'Admin.Catalog.Feature'
                ),
                'label_help_box' => $this->trans(
                    'The email will be sent to all users who have access to the Stock page. To modify permissions, go to [1]Advanced Parameters > Team[/1].',
                    'Admin.Catalog.Help',
                    [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->router->generate('admin_employees_index')
                        ),
                        '[/1]' => '</a>',
                    ]
                ),
                'disabling_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('available_date', DatePickerType::class, [
                'label' => $this->trans('Availability date', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'disabling_switch' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Stocks', 'Admin.Catalog.Feature'),
        ]);
    }
}
