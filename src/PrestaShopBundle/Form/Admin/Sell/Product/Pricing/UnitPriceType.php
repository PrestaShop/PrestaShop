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

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormHelper;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class UnitPriceType extends TranslatorAwareType
{
    private const ENABLED_GROUP = 'enabled_group';

    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param string $defaultCurrencyIsoCode
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        string $defaultCurrencyIsoCode
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('price_tax_excluded', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price per unit (tax excl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                    new Positive([
                        'groups' => [self::ENABLED_GROUP],
                    ]),
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ])
            ->add('price_tax_included', MoneyType::class, [
                'required' => false,
                'label' => $this->trans('Retail price per unit (tax incl.)', 'Admin.Catalog.Feature'),
                'attr' => ['data-display-price-precision' => FormHelper::DEFAULT_PRICE_PRECISION],
                'currency' => $this->defaultCurrencyIsoCode,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                    new Positive([
                        'groups' => [self::ENABLED_GROUP],
                    ]),
                ],
                'default_empty_data' => 0.0,
                'modify_all_shops' => true,
            ])
            ->add('unity', TextType::class, [
                'label' => $this->trans('Unit', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => ['placeholder' => $this->trans('Per kilo, per litre', 'Admin.Catalog.Help')],
                'modify_all_shops' => true,
                'empty_data' => '',
                'constraints' => [
                    new NotBlank([
                        'groups' => [self::ENABLED_GROUP],
                    ]),
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
            'label' => $this->trans('Display retail price per unit', 'Admin.Catalog.Feature'),
            'label_help_box' => $this->trans('Indicate the price for a single unit of the product. For instance, if you\'re selling fabrics, it would be the price per meter.', 'Admin.Catalog.Help'),
            'label_tag_name' => 'h3',
            'required' => false,
            'columns_number' => 4,
            'disabling_switch' => true,
            'disabled_value' => function (?array $data, FormInterface $form): bool {
                return $this->shouldBeDisabled($data, $form);
            },
            'validation_groups' => function (FormInterface $form): array {
                $shouldBeDisabled = $this->shouldBeDisabled($form->getData(), $form);

                return $shouldBeDisabled ? [] : [self::ENABLED_GROUP];
            },
        ]);
    }

    /**
     * Check based on form data and submitted data is the form should be disabled.
     *
     * @param array|null $data
     * @param FormInterface $form
     *
     * @return bool
     */
    private function shouldBeDisabled(?array $data, FormInterface $form): bool
    {
        $priceChild = $form->get('price_tax_excluded');
        $unityChild = $form->get('unity');
        $hasPrice = !empty($priceChild->getData()) || !empty($data['price_tax_excluded']);
        $hasUnity = !empty($unityChild->getData()) || !empty($data['unity']);

        return !$hasPrice && !$hasUnity;
    }
}
