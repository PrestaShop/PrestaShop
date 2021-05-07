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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use Currency;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class UnitPriceType extends TranslatorAwareType
{
    /**
     * @var Currency
     */
    private $defaultCurrency;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param Currency $defaultCurrency
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Currency $defaultCurrency
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price per unit (tax excl.)', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Indicate the price for a single unit of the product. For instance, if you\'re selling fabrics, it would be the price per meter.', 'Admin.Catalog.Help'),
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'currency' => $this->defaultCurrency->iso_code,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                ],
                'default_empty_data' => 0.0,
            ])
            ->add('unity', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => $this->trans('Per kilo, per litre', 'Admin.Catalog.Help')],
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
            'required' => false,
            'columns_number' => 4,
        ]);
    }
}
