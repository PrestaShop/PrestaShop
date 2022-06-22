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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class InvoiceOptionsType generates "Invoice options" form
 * in "Sell > Orders > Invoices" page.
 */
class InvoiceOptionsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $invoiceModelChoiceProvider;

    /**
     * @var int the next available invoice number
     */
    private $nextInvoiceNumber;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $invoiceModelChoiceProvider
     * @param int $nextInvoiceNumber the next available invoice number
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $invoiceModelChoiceProvider,
        $nextInvoiceNumber
    ) {
        parent::__construct($translator, $locales);
        $this->invoiceModelChoiceProvider = $invoiceModelChoiceProvider;
        $this->nextInvoiceNumber = $nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $featureTranslationKey = 'Admin.Orderscustomers.Feature';
        $helpTranslationKey = 'Admin.Orderscustomers.Help';

        $builder
            ->add(
                'enable_invoices',
                SwitchType::class,
                [
                    'required' => true,
                    'multistore_configuration_key' => 'PS_INVOICE',
                    'label' => $this->trans('Enable invoices', $featureTranslationKey),
                    'help' => $this->trans(
                        'If enabled, your customers will receive an invoice for the purchase.',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'enable_tax_breakdown',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_TAXES_BREAKDOWN',
                    'label' => $this->trans('Enable tax breakdown', $featureTranslationKey),
                    'help' => $this->trans(
                        'If required, show the total amount per rate of the corresponding tax.',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'enable_product_images',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_PDF_IMG_INVOICE',
                    'label' => $this->trans('Enable product image', $featureTranslationKey),
                    'help' => $this->trans(
                        'Adds an image in front of the product name on the invoice',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'invoice_prefix',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_PREFIX',
                    'label' => $this->trans('Invoice prefix', $featureTranslationKey),
                    'help' => $this->trans(
                        'Freely definable prefix for invoice number (e.g. #IN00001).',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'add_current_year',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_USE_YEAR',
                    'label' => $this->trans(
                        'Add current year to invoice number',
                        $featureTranslationKey
                    ),
                ]
            )
            ->add(
                'reset_number_annually',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_RESET',
                    'label' => $this->trans(
                        'Reset sequential invoice number at the beginning of the year',
                        $featureTranslationKey
                    ),
                ]
            )
            ->add(
                'year_position',
                ChoiceType::class,
                [
                    'choices' => [
                        $this->trans('After the sequential number', $featureTranslationKey) => 0,
                        $this->trans('Before the sequential number', $featureTranslationKey) => 1,
                    ],
                    'expanded' => true,
                    'multistore_configuration_key' => 'PS_INVOICE_YEAR_POS',
                    'label' => $this->trans('Position of the year date', $featureTranslationKey),
                ]
            )
            ->add(
                'invoice_number',
                IntegerType::class,
                [
                    'required' => false,
                    'multistore_configuration_key' => 'PS_INVOICE_START_NUMBER',
                    'label' => $this->trans('Invoice number', $featureTranslationKey),
                    'help' => $this->trans(
                        'The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (which is #%number%).',
                        $helpTranslationKey,
                        [
                            '%number%' => $this->nextInvoiceNumber,
                        ]
                    ),
                ]
            )
            ->add(
                'legal_free_text',
                TranslatableType::class,
                [
                    'type' => TextareaType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_LEGAL_FREE_TEXT',
                    'label' => $this->trans('Legal free text', $featureTranslationKey),
                    'help' => $this->trans(
                        'Use this field to show additional information on the invoice, below the payment methods summary (like specific legal information).',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'footer_text',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_FREE_TEXT',
                    'label' => $this->trans('Footer text', $featureTranslationKey),
                    'help' => $this->trans(
                        'This text will appear at the bottom of the invoice, below your company details.',
                        $helpTranslationKey
                    ),
                ]
            )
            ->add(
                'invoice_model',
                ChoiceType::class,
                [
                    'choices' => $this->invoiceModelChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'multistore_configuration_key' => 'PS_INVOICE_MODEL',
                    'label' => $this->trans('Invoice model', $featureTranslationKey),
                    'help' => $this->trans('Choose an invoice model.', $helpTranslationKey),
                ]
            )
            ->add(
                'use_disk_cache',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_PDF_USE_CACHE',
                    'label' => $this->trans(
                        'Use the disk as cache for PDF invoices',
                        $featureTranslationKey
                    ),
                    'help' => $this->trans(
                        'Saves memory but slows down the PDF generation.',
                        $helpTranslationKey
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['next_invoice_number'] = $this->nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Orderscustomers.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
