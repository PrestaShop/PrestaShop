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

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Reduction;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\ShippingInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TaxInclusionChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Responsible for creating form for price reduction
 */
class MinimumAmountType extends TranslatorAwareType implements EventSubscriberInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        parent::__construct($translator, $locales);
        $this->currencyDataProvider = $currencyDataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptValueField',
            FormEvents::PRE_SUBMIT => 'adaptValueField',
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', MoneyType::class, [
                'scale' => $options['scale'],
                'row_attr' => [
                    'class' => 'price-reduction-value',
                ],
                'default_empty_data' => 0,
            ])
            ->add('currency', CurrencyChoiceType::class, [
                'row_attr' => [
                    'class' => 'price-reduction-currency-selector',
                ],
            ])
            ->add('tax_included', TaxInclusionChoiceType::class)
            ->add('shipping_included', ShippingInclusionChoiceType::class)
        ;
        $builder->addEventSubscriber($this);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'scale' => 6,
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/minimum_amount.html.twig',
            ])
        ;
    }

    public function adaptValueField(FormEvent $event): void
    {
        $data = $event->getData();
        if (!isset($data['type'])) {
            return;
        }

        $form = $event->getForm();
        $valueField = $form->get('value');
        $options = $valueField->getConfig()->getOptions();

        // It is possible to have different values in same request, but different events, so if/else is essential
        // to make sure the form is built as expected during all events
        $form->add('value', MoneyType::class, [
            'scale' => $options['scale'],
            'currency' => $this->currencyDataProvider->getDefaultCurrencyIsoCode(),
            'attr' => [
                'data-currency' => $this->currencyDataProvider->getDefaultCurrencySymbol(),
            ],
            'row_attr' => [
                'class' => 'price-reduction-value',
            ],
            'default_empty_data' => 0,
        ]);
    }
}
