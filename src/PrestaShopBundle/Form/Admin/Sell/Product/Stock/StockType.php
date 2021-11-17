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
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class StockType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $packStockTypeChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $packStockTypeChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $packStockTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->packStockTypeChoiceProvider = $packStockTypeChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantities', QuantityType::class)
            ->add('options', StockOptionsType::class)
            ->add('virtual_product_file', VirtualProductFileType::class, [
                'virtual_product_file_id' => $options['virtual_product_file_id'] ?? null,
            ])
            ->add('pack_stock_type', ChoiceType::class, [
                'choices' => $this->packStockTypeChoiceProvider->getChoices(),
                'expanded' => true,
                'label' => $this->trans('Pack quantities', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'required' => false,
            ])
            ->add('availability', AvailabilityType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'required' => false,
            'virtual_product_file_id' => null,
            // Suppliers can be removed so there might be extra data during type switching
            'allow_extra_fields' => true,
        ]);
        $resolver->setAllowedTypes('virtual_product_file_id', ['int', 'null']);
    }
}
