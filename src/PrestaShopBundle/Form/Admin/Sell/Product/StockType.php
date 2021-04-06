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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class StockType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $outOfStockTypeChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $packStockTypeChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $outOfStockTypeChoiceProvider
     * @param FormChoiceProviderInterface $packStockTypeChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $outOfStockTypeChoiceProvider,
        FormChoiceProviderInterface $packStockTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->outOfStockTypeChoiceProvider = $outOfStockTypeChoiceProvider;
        $this->packStockTypeChoiceProvider = $packStockTypeChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', NumberType::class, [
                'required' => false,
                'label' => $this->trans('Quantity', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('minimal_quantity', NumberType::class, [
                'label' => $this->trans('Minimum quantity for sale', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('stock_location', TextType::class, [
                'label' => $this->trans('Stock location', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('low_stock_threshold', NumberType::class, [
                'label' => $this->trans('Low stock level', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new Type(['type' => 'numeric']),
                ],
                'required' => false,
            ])
            ->add('low_stock_alert', SwitchType::class, [
                'label' => $this->trans(
                    'Send me an email when the quantity is below or equals this level',
                    'Admin.Catalog.Feature'
                ),
            ])
            ->add('pack_stock_type', ChoiceType::class, [
                'choices' => $this->packStockTypeChoiceProvider->getChoices(),
            ])
            // @todo: will need to reuse some fields bellow combinations tab as shared "Availability preferences"
            ->add('out_of_stock_type', ChoiceType::class, [
                'choices' => $this->outOfStockTypeChoiceProvider->getChoices(),
            ])
            ->add('available_now_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when in stock', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('available_later_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans(
                    'Label when out of stock (and back order allowed)',
                    'Admin.Catalog.Feature'
                ),
                'required' => false,
            ])
            ->add('available_date', DatePickerType::class, [
                'label' => $this->trans('Availability date', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
        ;
    }
}
