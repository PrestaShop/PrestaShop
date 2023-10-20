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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use Currency;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductSupplierType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface&FormChoiceAttributeProviderInterface
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var string
     */
    private $defaultCurrencyIsoCode;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface&FormChoiceAttributeProviderInterface $currencyByIdChoiceProvider
     * @param string $defaultCurrencyIsoCode
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $currencyByIdChoiceProvider,
        string $defaultCurrencyIsoCode,
        CurrencyRepository $currencyRepository,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->defaultCurrencyIsoCode = $defaultCurrencyIsoCode;
        $this->currencyRepository = $currencyRepository;
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('supplier_id', HiddenType::class, [
                'required' => true,
            ])
            ->add('supplier_name', HiddenType::class, [
                'label' => $this->trans('Supplier', 'Admin.Global'),
                'required' => false,
            ])
            ->add('product_supplier_id', HiddenType::class, [
                'required' => false,
            ])
            ->add('reference', TextType::class, [
                'label' => $this->trans('Supplier reference', 'Admin.Catalog.Feature'),
                'constraints' => [
                    new TypedRegex(TypedRegex::TYPE_REFERENCE),
                    new Length([
                        'max' => Reference::MAX_LENGTH,
                    ]),
                ],
            ])
            ->add('price_tax_excluded', MoneyType::class, [
                'label' => $this->trans('Cost price (tax excl.)', 'Admin.Catalog.Feature'),
                'currency' => $this->defaultCurrencyIsoCode,
                'scale' => self::PRESTASHOP_DECIMALS,
                'attr' => ['data-display-price-precision' => self::PRESTASHOP_DECIMALS],
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'float']),
                    new PositiveOrZero(),
                ],
                'default_empty_data' => 0.0,
            ])
            ->add('currency_id', ChoiceType::class, [
                'label' => $this->trans('Currency', 'Admin.Global'),
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'choices' => $this->currencyByIdChoiceProvider->getChoices(),
                'choice_attr' => $this->currencyByIdChoiceProvider->getChoicesAttributes(),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $productSupplierData = $event->getData();
            if (empty($productSupplierData['currency_id'])) {
                return;
            }
            $currencyId = $productSupplierData['currency_id'];

            /** @var Currency $currency */
            $currency = $this->currencyRepository->get(new CurrencyId($currencyId));
            if ($currency->iso_code !== $this->defaultCurrencyIsoCode) {
                $form = $event->getForm();
                $form->add($this->formCloner->cloneForm($form->get('price_tax_excluded'), [
                    'currency' => $currency->iso_code,
                ]));
            }
        });
    }
}
