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

use PrestaShopBundle\Form\Admin\Sell\Product\Stock\QuantityType;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\StockOptionsType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CombinationStockType extends TranslatorAwareType
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
        $builder
            ->add('quantities', QuantityType::class)
            ->add('options', StockOptionsType::class)
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
            'label' => false,
        ]);
    }
}
