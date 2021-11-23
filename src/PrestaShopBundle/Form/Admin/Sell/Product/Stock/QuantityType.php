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

use PrestaShopBundle\Form\Admin\Type\DeltaQuantityType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class QuantityType extends TranslatorAwareType
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

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->stockManagementEnabled) {
            $urlParameters = !empty($options['product_id']) ? ['productId' => (int) $options['product_id']] : [];
            $stockMovementsUrl = $this->router->generate('admin_stock_movements_overview', $urlParameters);

            $builder
                ->add('quantity', DeltaQuantityType::class, [
                    'required' => false,
                    'label' => $this->trans('Edit quantity', 'Admin.Catalog.Feature'),
                    'label_tag_name' => 'h4',
                    'constraints' => [
                        new NotBlank(),
                        new Type(['type' => 'numeric']),
                    ],
                    'default_empty_data' => 0,
                ])
                ->add('stock_movements', EntitySearchInputType::class, [
                    'required' => false,
                    'label' => $this->trans('Recent stock movements', 'Admin.Catalog.Feature'),
                    'label_tag_name' => 'h4',
                    'layout' => 'table',
                    'entry_type' => StockMovementType::class,
                    // No search input
                    'allow_search' => false,
                    // No delete button
                    'allow_delete' => false,
                    'external_link' => [
                        'text' => $this->trans('[1]View all stock movements[/1]', 'Admin.Catalog.Feature'),
                        'href' => $stockMovementsUrl,
                    ],
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
        $resolver
            ->setDefaults([
                'label' => $this->trans('Quantities', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'required' => false,
                'product_id' => null,
            ])
            ->setAllowedTypes('product_id', ['null', 'int'])
        ;
    }
}
