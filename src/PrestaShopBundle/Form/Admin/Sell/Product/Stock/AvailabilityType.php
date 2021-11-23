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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class AvailabilityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $outOfStockTypeChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $outOfStockTypeChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $outOfStockTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->outOfStockTypeChoiceProvider = $outOfStockTypeChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('out_of_stock_type', ChoiceType::class, [
                'choices' => $this->outOfStockTypeChoiceProvider->getChoices(),
                'label' => $this->trans('Behavior when out of stock', 'Admin.Catalog.Feature'),
                'expanded' => true,
                'column_breaker' => true,
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
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
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
            'label' => $this->trans('Availability preferences', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
